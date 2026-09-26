<?php

declare(strict_types=1);

use App\Core\Config;
use App\Core\Env;

define('BASE_PATH', __DIR__);

$composerAutoload = BASE_PATH . '/vendor/autoload.php';
if (is_file($composerAutoload)) {
    require_once $composerAutoload;
}

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require_once $file;
    }
});

Env::load(BASE_PATH . '/.env');

date_default_timezone_set((string) Config::get('app.timezone', 'UTC'));

require_once BASE_PATH . '/app/Helpers/common.php';
