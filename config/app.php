<?php

declare(strict_types=1);

use App\Core\Env;

return [
    'name' => Env::get('APP_NAME'),
    'base_url' => rtrim((string) Env::get('APP_BASE_URL'), '/'),
    'env' => Env::get('APP_ENV'),
    'debug' => filter_var(Env::get('APP_DEBUG'), FILTER_VALIDATE_BOOL),
    'timezone' => Env::get('APP_TIMEZONE'),
    'session_name' => Env::get('SESSION_NAME'),
    'upload_dir' => dirname(__DIR__) . '/storage/uploads',
    'max_upload_size' => (int) Env::get('MAX_UPLOAD_SIZE'),
];
