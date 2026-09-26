<?php

declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'PasteBox',
    'base_url' => rtrim((string) (getenv('APP_BASE_URL') ?: 'https://pastebox.ir'), '/'),
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'timezone' => getenv('APP_TIMEZONE') ?: 'Asia/Tehran',
    'session_name' => getenv('SESSION_NAME') ?: 'pastebox_session',
    'upload_dir' => dirname(__DIR__) . '/storage/uploads',
    'max_upload_size' => (int) (getenv('MAX_UPLOAD_SIZE') ?: 268435456),
];
