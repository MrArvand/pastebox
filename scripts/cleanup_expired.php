<?php

declare(strict_types=1);

use App\Models\Paste;
use App\Services\FileUploadService;

require_once dirname(__DIR__) . '/bootstrap.php';

$pasteModel = new Paste();
$uploadService = new FileUploadService();
$now = new DateTimeImmutable();

$records = $pasteModel->expiredWithAttachments($now);
$deletedFiles = 0;

foreach ($records as $record) {
    $relativePath = $record['storage_path'] ?? null;
    if (is_string($relativePath) && $relativePath !== '') {
        $fullPath = $uploadService->absolutePath($relativePath);
        if (is_file($fullPath)) {
            @unlink($fullPath);
            $deletedFiles++;
        }
    }
}

$deletedPastes = $pasteModel->deleteExpired($now);

$logDir = dirname(__DIR__) . '/storage/logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0755, true);
}

$logLine = sprintf(
    "[%s] deleted_pastes=%d deleted_files=%d\n",
    date('Y-m-d H:i:s'),
    $deletedPastes,
    $deletedFiles
);
file_put_contents($logDir . '/cleanup.log', $logLine, FILE_APPEND);

echo $logLine;
