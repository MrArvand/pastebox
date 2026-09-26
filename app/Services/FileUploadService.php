<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;
use RuntimeException;

final class FileUploadService
{
    /**
     * Extension => acceptable MIME types (finfo and browsers vary).
     *
     * @var array<string, list<string>>
     */
    private const EXTENSION_MIME_MAP = [
        // Text & data
        'txt' => ['text/plain', 'application/octet-stream'],
        'md' => ['text/plain', 'text/markdown', 'text/x-markdown', 'application/octet-stream'],
        'markdown' => ['text/plain', 'text/markdown', 'text/x-markdown', 'application/octet-stream'],
        'json' => ['application/json', 'text/plain', 'application/octet-stream'],
        'csv' => ['text/csv', 'text/plain', 'application/csv', 'application/vnd.ms-excel', 'application/octet-stream'],
        'tsv' => ['text/tab-separated-values', 'text/plain', 'application/octet-stream'],
        'log' => ['text/plain', 'application/octet-stream'],
        'xml' => ['text/xml', 'application/xml', 'application/octet-stream'],
        'yaml' => ['text/yaml', 'text/plain', 'application/x-yaml', 'application/yaml', 'application/octet-stream'],
        'yml' => ['text/yaml', 'text/plain', 'application/x-yaml', 'application/yaml', 'application/octet-stream'],
        'ini' => ['text/plain', 'application/octet-stream'],
        'conf' => ['text/plain', 'application/octet-stream'],
        'sql' => ['text/plain', 'application/sql', 'application/x-sql', 'application/octet-stream'],
        // Documents
        'pdf' => ['application/pdf', 'application/octet-stream'],
        'doc' => ['application/msword', 'application/vnd.ms-word', 'application/octet-stream'],
        'docx' => [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/zip',
            'application/x-zip-compressed',
            'application/octet-stream',
        ],
        'xls' => ['application/vnd.ms-excel', 'application/msexcel', 'application/octet-stream'],
        'xlsx' => [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/x-zip-compressed',
            'application/octet-stream',
        ],
        'ppt' => ['application/vnd.ms-powerpoint', 'application/mspowerpoint', 'application/octet-stream'],
        'pptx' => [
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/zip',
            'application/x-zip-compressed',
            'application/octet-stream',
        ],
        'odt' => ['application/vnd.oasis.opendocument.text', 'application/zip', 'application/octet-stream'],
        'ods' => ['application/vnd.oasis.opendocument.spreadsheet', 'application/zip', 'application/octet-stream'],
        'odp' => ['application/vnd.oasis.opendocument.presentation', 'application/zip', 'application/octet-stream'],
        'rtf' => ['text/rtf', 'application/rtf', 'application/octet-stream'],
        // Images
        'png' => ['image/png', 'application/octet-stream'],
        'jpg' => ['image/jpeg', 'application/octet-stream'],
        'jpeg' => ['image/jpeg', 'application/octet-stream'],
        'gif' => ['image/gif', 'application/octet-stream'],
        'webp' => ['image/webp', 'application/octet-stream'],
        'bmp' => ['image/bmp', 'image/x-ms-bmp', 'application/octet-stream'],
        'svg' => ['image/svg+xml', 'text/plain', 'application/octet-stream'],
        'ico' => ['image/x-icon', 'image/vnd.microsoft.icon', 'application/octet-stream'],
        'tif' => ['image/tiff', 'application/octet-stream'],
        'tiff' => ['image/tiff', 'application/octet-stream'],
        'heic' => ['image/heic', 'image/heif', 'application/octet-stream'],
        'heif' => ['image/heif', 'image/heic', 'application/octet-stream'],
        // Audio
        'mp3' => ['audio/mpeg', 'audio/mp3', 'audio/x-mpeg', 'application/octet-stream'],
        'wav' => ['audio/wav', 'audio/x-wav', 'audio/wave', 'audio/vnd.wave', 'application/octet-stream'],
        'ogg' => ['audio/ogg', 'application/ogg', 'application/octet-stream'],
        'oga' => ['audio/ogg', 'application/ogg', 'application/octet-stream'],
        'opus' => ['audio/opus', 'audio/ogg', 'application/octet-stream'],
        'm4a' => ['audio/mp4', 'audio/x-m4a', 'audio/m4a', 'application/octet-stream'],
        'aac' => ['audio/aac', 'audio/x-aac', 'audio/mp4', 'application/octet-stream'],
        'flac' => ['audio/flac', 'audio/x-flac', 'application/flac', 'application/octet-stream'],
        'wma' => ['audio/x-ms-wma', 'application/octet-stream'],
        'mid' => ['audio/midi', 'audio/mid', 'application/octet-stream'],
        'midi' => ['audio/midi', 'audio/mid', 'application/octet-stream'],
        'aiff' => ['audio/aiff', 'audio/x-aiff', 'application/octet-stream'],
        'aif' => ['audio/aiff', 'audio/x-aiff', 'application/octet-stream'],
        // Video
        'mp4' => ['video/mp4', 'application/mp4', 'audio/mp4', 'application/octet-stream'],
        'm4v' => ['video/mp4', 'video/x-m4v', 'application/octet-stream'],
        'webm' => ['video/webm', 'audio/webm', 'application/octet-stream'],
        'mov' => ['video/quicktime', 'application/octet-stream'],
        'avi' => ['video/x-msvideo', 'video/avi', 'video/msvideo', 'application/octet-stream'],
        'mkv' => ['video/x-matroska', 'application/octet-stream'],
        'wmv' => ['video/x-ms-wmv', 'application/octet-stream'],
        'mpeg' => ['video/mpeg', 'application/octet-stream'],
        'mpg' => ['video/mpeg', 'application/octet-stream'],
        '3gp' => ['video/3gpp', 'application/octet-stream'],
        // Archives
        'zip' => ['application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/octet-stream'],
        'rar' => ['application/vnd.rar', 'application/x-rar-compressed', 'application/x-rar', 'application/octet-stream'],
        '7z' => ['application/x-7z-compressed', 'application/octet-stream'],
        'tar' => ['application/x-tar', 'application/octet-stream'],
        'gz' => ['application/gzip', 'application/x-gzip', 'application/octet-stream'],
        'bz2' => ['application/x-bzip2', 'application/octet-stream'],
        // Code & web
        'html' => ['text/html', 'application/octet-stream'],
        'htm' => ['text/html', 'application/octet-stream'],
        'css' => ['text/css', 'text/plain', 'application/octet-stream'],
        'js' => ['text/javascript', 'application/javascript', 'application/x-javascript', 'text/plain', 'application/octet-stream'],
        'php' => ['text/plain', 'text/x-php', 'application/x-php', 'application/octet-stream'],
        'py' => ['text/x-python', 'text/plain', 'application/octet-stream'],
        'java' => ['text/x-java-source', 'text/plain', 'application/octet-stream'],
        'c' => ['text/x-c', 'text/plain', 'application/octet-stream'],
        'cpp' => ['text/x-c', 'text/plain', 'application/octet-stream'],
        'h' => ['text/x-c', 'text/plain', 'application/octet-stream'],
        'sh' => ['text/x-shellscript', 'application/x-sh', 'text/plain', 'application/octet-stream'],
        'bat' => ['text/plain', 'application/x-msdos-program', 'application/octet-stream'],
    ];

    /**
     * @param array<string, mixed>|null $file
     * @return array<string, mixed>|null
     */
    public function save(?array $file): ?array
    {
        if ($file === null || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $uploadError = (int) ($file['error'] ?? UPLOAD_ERR_OK);
        if ($uploadError !== UPLOAD_ERR_OK) {
            $maxSize = (int) Config::get('app.max_upload_size', 268435456);
            if (in_array($uploadError, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true)) {
                throw new RuntimeException(
                    'حجم فایل بیش از حد مجاز است (حداکثر ' . format_bytes($maxSize) . ').'
                );
            }

            throw new RuntimeException('آپلود فایل با خطا مواجه شد.');
        }

        $size = (int) ($file['size'] ?? 0);
        $maxSize = (int) Config::get('app.max_upload_size', 268435456);
        if ($size <= 0 || $size > $maxSize) {
            throw new RuntimeException(
                'حجم فایل بیش از حد مجاز است (حداکثر ' . format_bytes($maxSize) . ').'
            );
        }

        $originalName = (string) ($file['name'] ?? '');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($extension === '' || !isset(self::EXTENSION_MIME_MAP[$extension])) {
            throw new RuntimeException('فرمت فایل مجاز نیست.');
        }

        $tmpPath = (string) ($file['tmp_name'] ?? '');
        if (!is_uploaded_file($tmpPath)) {
            throw new RuntimeException('فایل آپلود شده معتبر نیست.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $this->normalizeMimeType((string) $finfo->file($tmpPath));
        if (!$this->isMimeAllowed($extension, $mimeType)) {
            throw new RuntimeException('نوع فایل پشتیبانی نمی‌شود.');
        }

        $uploadRoot = (string) Config::get('app.upload_dir');
        $dateDir = date('Y/m/d');
        $targetDir = $uploadRoot . DIRECTORY_SEPARATOR . $dateDir;
        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            throw new RuntimeException('امکان ساخت مسیر ذخیره فایل وجود ندارد.');
        }

        $storedName = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $targetDir . DIRECTORY_SEPARATOR . $storedName;
        if (!move_uploaded_file($tmpPath, $destination)) {
            throw new RuntimeException('ذخیره فایل انجام نشد.');
        }

        return [
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'mime_type' => $mimeType,
            'size_bytes' => $size,
            'storage_path' => str_replace('\\', '/', $dateDir . '/' . $storedName),
        ];
    }

    /** @return list<string> */
    public static function allowedExtensions(): array
    {
        return array_keys(self::EXTENSION_MIME_MAP);
    }

    public static function allowedFormatsHint(): string
    {
        return 'Office (Word, Excel, PowerPoint)، PDF، تصویر، صدا، ویدیو، فشرده، متن، کد و...';
    }

    private function normalizeMimeType(string $mimeType): string
    {
        $mimeType = strtolower(trim($mimeType));
        if (str_contains($mimeType, ';')) {
            $mimeType = trim(explode(';', $mimeType, 2)[0]);
        }

        return $mimeType;
    }

    private function isMimeAllowed(string $extension, string $mimeType): bool
    {
        $allowed = self::EXTENSION_MIME_MAP[$extension] ?? [];

        return in_array($mimeType, $allowed, true);
    }

    public function deleteByRelativePath(string $relativePath): void
    {
        $root = rtrim((string) Config::get('app.upload_dir'), '/\\');
        $clean = ltrim(str_replace(['..\\', '../'], '', $relativePath), '/\\');
        $fullPath = $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $clean);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    public function absolutePath(string $relativePath): string
    {
        $root = rtrim((string) Config::get('app.upload_dir'), '/\\');
        $clean = ltrim(str_replace(['..\\', '../'], '', $relativePath), '/\\');
        return $root . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $clean);
    }
}
