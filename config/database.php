<?php

declare(strict_types=1);

use App\Core\Env;

return [
    'host' => Env::get('DB_HOST'),
    'port' => (int) Env::get('DB_PORT'),
    'database' => Env::get('DB_DATABASE'),
    'username' => Env::get('DB_USERNAME'),
    'password' => Env::get('DB_PASSWORD'),
    'charset' => Env::get('DB_CHARSET'),
];
