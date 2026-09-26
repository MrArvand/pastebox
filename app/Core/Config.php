<?php

declare(strict_types=1);

namespace App\Core;

final class Config
{
    /** @var array<string, mixed>|null */
    private static ?array $config = null;

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();

        $segments = explode('.', $key);
        $value = self::$config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }

        return $value;
    }

    private static function load(): void
    {
        if (self::$config !== null) {
            return;
        }

        self::$config = [
            'app' => require BASE_PATH . '/config/app.php',
            'database' => require BASE_PATH . '/config/database.php',
        ];
    }
}
