<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Config;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Services\FileUploadService;
use App\Services\PasteQrService;
use App\Services\PasteService;
use App\Services\RateLimiter;

final class PasteController
{
    private PasteService $pasteService;
    private PasteQrService $pasteQrService;
    private RateLimiter $rateLimiter;

    public function __construct()
    {
        $this->pasteService = new PasteService();
        $this->pasteQrService = new PasteQrService();
        $this->rateLimiter = new RateLimiter();
    }

    public function createForm(Request $request): void
    {
        $maxUploadSize = (int) Config::get('app.max_upload_size', 268435456);

        View::render('paste.create', [
            'title' => 'PasteBox | اشتراک‌گذاری امن پیست و فایل',
            'error' => flash('error'),
            'successLink' => flash('success_link'),
            'successCode' => flash('success_code'),
            'maxUploadSize' => $maxUploadSize,
            'maxUploadSizeLabel' => format_bytes($maxUploadSize),
            'allowedFormatsHint' => FileUploadService::allowedFormatsHint(),
            'allowedFileAccept' => implode(',', array_map(
                static fn (string $ext): string => '.' . $ext,
                FileUploadService::allowedExtensions()
            )),
        ]);
    }

    /** @param array<string,string> $params */
    public function pastesStats(Request $request, array $params): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store, max-age=0');
        header('X-Content-Type-Options: nosniff');

        $count = $this->pasteService->latestPasteId();
        echo json_encode(['count' => $count]);
        exit;
    }

    /** @param array<string,string> $params */
    public function pasteAvailability(Request $request, array $params): void
    {
        if (!$this->rateLimiter->hit('paste:lookup:' . $request->ip(), 30, 300)) {
            Response::json(['ok' => false, 'error' => 'تعداد درخواست‌ها زیاد است. چند دقیقه بعد دوباره تلاش کنید.'], 429);
        }

        $code = (string) ($params['code'] ?? '');
        $paste = $this->pasteService->findPasteByCode($code);
        if ($paste === null) {
            Response::json(['ok' => false, 'error' => 'کد کامل یا معتبر نیست؛ دوباره بررسی کنید.'], 404);
        }

        if ($this->pasteService->isExpired($paste)) {
            Response::json(['ok' => false, 'error' => 'این پیست منقضی شده است.'], 410);
        }

        Response::json(['ok' => true]);
    }

    /** @param array<string,string> $params */
    public function store(Request $request, array $params): void
    {
        $wantsJson = $request->wantsJson();

        if (!$this->rateLimiter->hit('paste:create:' . $request->ip(), 20, 300)) {
            $message = 'تعداد درخواست‌ها زیاد است. چند دقیقه بعد دوباره تلاش کنید.';
            if ($wantsJson) {
                Response::json(['ok' => false, 'error' => $message], 429);
            }
            flash('error', $message);
            Response::redirect('/');
        }

        if (!csrf_validate((string) $request->input('_csrf'))) {
            if ($wantsJson) {
                Response::json(['ok' => false, 'error' => 'درخواست نامعتبر است.'], 419);
            }
            Response::abort(419, 'درخواست نامعتبر است.');
        }

        try {
            $created = $this->pasteService->createPaste(
                (string) $request->input('content', ''),
                (string) $request->input('expires_in', '1d'),
                $request->input('password') !== null ? (string) $request->input('password') : null,
                $request->input('burn_after_reading') !== null,
                $request->files()['attachment'] ?? null
            );
        } catch (\Throwable $throwable) {
            if ($wantsJson) {
                Response::json(['ok' => false, 'error' => $throwable->getMessage()], 422);
            }
            flash('error', $throwable->getMessage());
            Response::redirect('/');
        }

        flash('success_link', app_url((string) $created['short_code']));
        flash('success_code', (string) $created['short_code']);

        if ($wantsJson) {
            Response::json(['ok' => true, 'redirect' => '/']);
        }

        Response::redirect('/');
    }

    /** @param array<string,string> $params */
    public function show(Request $request, array $params): void
    {
        $code = (string) ($params['code'] ?? '');
        $paste = $this->pasteService->findPasteByCode($code);
        if ($paste === null) {
            View::render('errors.404', ['title' => 'پیست پیدا نشد'], 404);
            return;
        }

        if ($this->pasteService->isExpired($paste)) {
            View::render('errors.410', ['title' => 'پیست منقضی شده'], 410);
            return;
        }

        $unlocked = (bool) $request->sessionGet('paste_unlocked_' . $code, false);
        $needsPassword = $this->pasteService->requiresPassword($paste);

        if ($needsPassword && !$unlocked) {
            View::render('paste.unlock', [
                'title' => 'این پیست محافظت شده است',
                'code' => $code,
                'error' => flash('error'),
            ]);
            return;
        }

        $attachments = $this->pasteService->attachmentsForPaste((int) $paste['id']);
        $burnAfterReading = $this->pasteService->burnAfterReadingEnabled($paste);

        if ($burnAfterReading) {
            $this->pasteService->burnPaste($paste);
            $request->sessionSet('paste_unlocked_' . $code, false);
        }

        View::render('paste.view', [
            'title' => 'مشاهده پیست ' . $code,
            'paste' => $paste,
            'attachments' => $attachments,
            'pasteUrl' => app_url($code),
            'burnAfterReading' => $burnAfterReading,
        ]);
    }

    /** @param array<string,string> $params */
    public function unlock(Request $request, array $params): void
    {
        $code = (string) ($params['code'] ?? '');
        if (!csrf_validate((string) $request->input('_csrf'))) {
            Response::abort(419, 'درخواست نامعتبر است.');
        }

        if (!$this->rateLimiter->hit('paste:unlock:' . $request->ip() . ':' . $code, 10, 300)) {
            flash('error', 'تعداد تلاش‌ها زیاد است. لطفا کمی بعد دوباره تلاش کنید.');
            Response::redirect('/' . $code);
        }

        $paste = $this->pasteService->findPasteByCode($code);
        if ($paste === null || $this->pasteService->isExpired($paste)) {
            View::render('errors.410', ['title' => 'پیست در دسترس نیست'], 410);
            return;
        }

        $password = (string) $request->input('password', '');
        if (!$this->pasteService->verifyPassword($paste, $password)) {
            flash('error', 'رمز عبور صحیح نیست.');
            Response::redirect('/' . $code);
        }

        $request->sessionSet('paste_unlocked_' . $code, true);
        Response::redirect('/' . $code);
    }

    /** @param array<string,string> $params */
    public function downloadAttachment(Request $request, array $params): void
    {
        $id = (int) ($params['id'] ?? 0);
        if ($id <= 0) {
            View::render('errors.404', ['title' => 'فایل پیدا نشد'], 404);
            return;
        }

        $attachment = $this->pasteService->findAttachment($id);
        if ($attachment === null) {
            View::render('errors.404', ['title' => 'فایل پیدا نشد'], 404);
            return;
        }

        $paste = $this->pasteService->findPasteByCode((string) $attachment['short_code']);
        if ($paste === null || $this->pasteService->isExpired($paste)) {
            View::render('errors.410', ['title' => 'فایل دیگر در دسترس نیست'], 410);
            return;
        }
        if (
            $this->pasteService->requiresPassword($paste) &&
            !(bool) $request->sessionGet('paste_unlocked_' . (string) $paste['short_code'], false)
        ) {
            flash('error', 'ابتدا باید پیست را با رمز عبور باز کنید.');
            Response::redirect('/' . (string) $paste['short_code']);
        }

        $filePath = $this->pasteService->filePathForAttachment($attachment);
        if (!is_file($filePath)) {
            View::render('errors.404', ['title' => 'فایل روی سرور موجود نیست'], 404);
            return;
        }

        $downloadName = str_replace('"', '', basename((string) $attachment['original_name']));
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $attachment['mime_type']);
        header('Content-Disposition: attachment; filename="' . $downloadName . '"');
        header('Content-Length: ' . filesize($filePath));
        header('X-Content-Type-Options: nosniff');
        readfile($filePath);
        exit;
    }

    /** @param array<string,string> $params */
    public function qrSvg(Request $request, array $params): void
    {
        $code = (string) ($params['code'] ?? '');
        $paste = $this->pasteService->findPasteByCode($code);
        if ($paste === null) {
            View::render('errors.404', ['title' => 'پیست پیدا نشد'], 404);
            return;
        }

        if ($this->pasteService->isExpired($paste)) {
            View::render('errors.410', ['title' => 'پیست منقضی شده'], 410);
            return;
        }

        $svg = $this->pasteQrService->svgForUrl(app_url($code));
        header('Content-Type: image/svg+xml; charset=utf-8');
        header('Cache-Control: public, max-age=300');
        header('X-Content-Type-Options: nosniff');
        echo $svg;
        exit;
    }
}
