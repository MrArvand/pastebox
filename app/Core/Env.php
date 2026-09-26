<?php

declare(strict_types=1);

namespace App\Core;

final class Env
{
    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return;
        }

        foreach ($lines as $index => $line) {
            if ($index === 0) {
                $line = preg_replace('/^\xEF\xBB\xBF/', '', $line) ?? $line;
            }

            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = trim(substr($line, 7));
            }

            $separator = strpos($line, '=');
            if ($separator === false) {
                continue;
            }

            $name = trim(substr($line, 0, $separator));
            if (!preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $name)) {
                continue;
            }

            if (getenv($name) !== false || array_key_exists($name, $_ENV)) {
                continue;
            }

            $value = self::parseValue(trim(substr($line, $separator + 1)));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
            putenv($name . '=' . $value);
        }
    }

    public static function get(string $key, ?string $default = null): ?string
    {
        if (array_key_exists($key, $_ENV) && is_string($_ENV[$key])) {
            return $_ENV[$key];
        }

        if (array_key_exists($key, $_SERVER) && is_string($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        $value = getenv($key);
        if ($value === false) {
            return $default;
        }

        return $value;
    }

    private static function parseValue(string $value): string
    {
        if ($value === '') {
            return '';
        }

        $quote = $value[0];
        if (($quote === '"' || $quote === "'") && str_ends_with($value, $quote) && strlen($value) >= 2) {
            return substr($value, 1, -1);
        }

        $comment = strpos($value, ' #');
        if ($comment !== false) {
            return rtrim(substr($value, 0, $comment));
        }

        return $value;
    }
}
