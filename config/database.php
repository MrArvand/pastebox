<?php

declare(strict_types=1);

return [
    'host' => getenv('DB_HOST') ?: '127.0.0.1',
    'port' => (int) (getenv('DB_PORT') ?: 3306),
    'database' => getenv('DB_DATABASE') ?: 'pasteb_ox',
    'username' => getenv('DB_USERNAME') ?: 'pasteb_ox',
    'password' => getenv('DB_PASSWORD') ?: 'eY0-aX0_mH4_zE8_',
    'charset' => getenv('DB_CHARSET') ?: 'utf8mb4',
];
