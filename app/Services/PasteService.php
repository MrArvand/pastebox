<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AppCounter;
use App\Models\Attachment;
use App\Models\Paste;
use DateInterval;
use DateTimeImmutable;
use PDOException;
use RuntimeException;

final class PasteService
{
    private const EXPIRATION_OPTIONS = [
        '5m' => 'PT5M',
        '10m' => 'PT10M',
        '1h' => 'PT1H',
        '1d' => 'P1D',
        '7d' => 'P7D',
        '14d' => 'P14D',
    ];

    private Paste $pasteModel;
    private Attachment $attachmentModel;
    private AppCounter $appCounter;
    private FileUploadService $uploadService;

    public function __construct()
    {
        $this->pasteModel = new Paste();
        $this->attachmentModel = new Attachment();
        $this->appCounter = new AppCounter();
        $this->uploadService = new FileUploadService();
    }

    /** @return array<string, mixed> */
    public function createPaste(
        string $content,
        string $expirationKey,
        ?string $password,
        bool $burnAfterReading,
        ?array $file
    ): array
    {
        $content = trim($content);
        if ($content === '') {
            throw new RuntimeException('پیست نمی‌تواند خالی باشد.');
        }

        if (mb_strlen($content) > 50000) {
            throw new RuntimeException('پیست بیش از حد مجاز است.');
        }

        if (!array_key_exists($expirationKey, self::EXPIRATION_OPTIONS)) {
            throw new RuntimeException('زمان انقضا معتبر نیست.');
        }

        $now = new DateTimeImmutable();
        $expiresAt = $now->add(new DateInterval(self::EXPIRATION_OPTIONS[$expirationKey]));

        $uuid = $this->generateUuidV4();
        $passwordHash = $password !== null && trim($password) !== ''
            ? password_hash($password, PASSWORD_DEFAULT)
            : null;

        $attachmentPayload = $this->uploadService->save($file);

        $shortCode = '';
        $created = false;
        $attempts = 0;
        while ($attempts < 10) {
            $attempts++;
            $shortCode = $this->generateUniqueShortCode();

            $this->pasteModel->beginTransaction();
            try {
                $pasteId = $this->pasteModel->create([
                    'uuid' => $uuid,
                    'short_code' => $shortCode,
                    'content' => $content,
                    'password_hash' => $passwordHash,
                    'burn_after_reading' => $burnAfterReading ? 1 : 0,
                    'expires_at' => $expiresAt->format('Y-m-d H:i:s'),
                    'created_at' => $now->format('Y-m-d H:i:s'),
                ]);

                if ($attachmentPayload !== null) {
                    $this->attachmentModel->create([
                        'paste_id' => $pasteId,
                        'original_name' => $attachmentPayload['original_name'],
                        'stored_name' => $attachmentPayload['stored_name'],
                        'mime_type' => $attachmentPayload['mime_type'],
                        'size_bytes' => $attachmentPayload['size_bytes'],
                        'storage_path' => $attachmentPayload['storage_path'],
                        'created_at' => $now->format('Y-m-d H:i:s'),
                    ]);
                }

                $this->appCounter->incrementPastesCreated();

                $this->pasteModel->commit();
                $created = true;
                break;
            } catch (PDOException $exception) {
                $this->pasteModel->rollBack();
                if ($this->isDuplicateShortCodeError($exception)) {
                    continue;
                }
                if ($attachmentPayload !== null) {
                    $this->uploadService->deleteByRelativePath((string) $attachmentPayload['storage_path']);
                }
                throw $exception;
            } catch (\Throwable $throwable) {
                $this->pasteModel->rollBack();
                if ($attachmentPayload !== null) {
                    $this->uploadService->deleteByRelativePath((string) $attachmentPayload['storage_path']);
                }
                throw $throwable;
            }
        }

        if ($shortCode === '' || !$created) {
            throw new RuntimeException('تولید لینک یکتا در حال حاضر امکان‌پذیر نیست.');
        }

        $createdPaste = $this->pasteModel->findByCode($shortCode);
        if ($createdPaste === null) {
            throw new RuntimeException('خطا در بازیابی پیست ایجاد شده.');
        }

        return $createdPaste;
    }

    /** @return array<string, mixed>|null */
    public function findPasteByCode(string $code): ?array
    {
        return $this->pasteModel->findByCode($code);
    }

    /**
     * Lifetime creates: authoritative counter row + MAX(id) fallback (covers hosts where information_schema was wrong).
     */
    public function latestPasteId(): int
    {
        return max($this->appCounter->pastesCreatedTotal(), $this->pasteModel->maxRowId());
    }

    public function isExpired(array $paste): bool
    {
        $expiresAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string) ($paste['expires_at'] ?? ''));
        if ($expiresAt === false) {
            return true;
        }

        return $expiresAt <= new DateTimeImmutable();
    }

    public function requiresPassword(array $paste): bool
    {
        $hash = $paste['password_hash'] ?? null;
        return is_string($hash) && $hash !== '';
    }

    public function burnAfterReadingEnabled(array $paste): bool
    {
        return (int) ($paste['burn_after_reading'] ?? 0) === 1;
    }

    public function verifyPassword(array $paste, string $password): bool
    {
        $hash = (string) ($paste['password_hash'] ?? '');
        if ($hash === '') {
            return true;
        }

        return password_verify($password, $hash);
    }

    /** @return array<int, array<string, mixed>> */
    public function attachmentsForPaste(int $pasteId): array
    {
        return $this->attachmentModel->findByPasteId($pasteId);
    }

    /** @return array<string, mixed>|null */
    public function findAttachment(int $id): ?array
    {
        return $this->attachmentModel->findById($id);
    }

    public function filePathForAttachment(array $attachment): string
    {
        return $this->uploadService->absolutePath((string) $attachment['storage_path']);
    }

    public function removeStoredFile(string $relativePath): void
    {
        $this->uploadService->deleteByRelativePath($relativePath);
    }

    public function burnPaste(array $paste): void
    {
        $pasteId = (int) ($paste['id'] ?? 0);
        if ($pasteId <= 0) {
            return;
        }

        $attachments = $this->attachmentModel->findByPasteId($pasteId);

        $this->pasteModel->beginTransaction();
        try {
            $this->pasteModel->deleteById($pasteId);
            $this->pasteModel->commit();
        } catch (\Throwable $throwable) {
            $this->pasteModel->rollBack();
            throw $throwable;
        }

        foreach ($attachments as $attachment) {
            $relativePath = (string) ($attachment['storage_path'] ?? '');
            if ($relativePath === '') {
                continue;
            }

            $this->uploadService->deleteByRelativePath($relativePath);
        }
    }

    private function generateUniqueShortCode(int $maxRetries = 40): string
    {
        for ($i = 0; $i < $maxRetries; $i++) {
            $code = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
            if (!$this->pasteModel->existsByCode($code)) {
                return $code;
            }
        }

        throw new RuntimeException('تولید لینک یکتا در حال حاضر امکان‌پذیر نیست.');
    }

    private function generateUuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function isDuplicateShortCodeError(PDOException $exception): bool
    {
        $sqlState = (string) ($exception->errorInfo[0] ?? '');
        $message = $exception->getMessage();
        return $sqlState === '23000' && str_contains($message, 'short_code');
    }
}
