<?php

declare(strict_types=1);

use App\Core\Env;

return [
    'name' => Env::get('APP_NAME') ?: 'PasteBox',
    'base_url' => rtrim((string) (Env::get('APP_BASE_URL') ?: 'https://pastebox.ir'), '/'),
    'env' => Env::get('APP_ENV') ?: 'production',
    'debug' => filter_var(Env::get('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOL),
    'timezone' => Env::get('APP_TIMEZONE') ?: 'Asia/Tehran',
    'session_name' => Env::get('SESSION_NAME') ?: 'pastebox_session',
    'upload_dir' => dirname(__DIR__) . '/storage/uploads',
    'max_upload_size' => (int) (Env::get('MAX_UPLOAD_SIZE') ?: 268435456),
];
