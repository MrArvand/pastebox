<?php

declare(strict_types=1);

use App\Core\Config;

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('app_url')) {
    function app_url(string $path = ''): string
    {
        $base = (string) Config::get('app.base_url', '');
        $normalizedPath = ltrim($path, '/');
        return $normalizedPath === '' ? $base : $base . '/' . $normalizedPath;
    }
}

if (!function_exists('asset_url')) {
    function asset_url(string $path): string
    {
        $normalizedPath = ltrim($path, '/');
        $url = app_url($normalizedPath);
        $fullPath = BASE_PATH . '/public/' . $normalizedPath;

        if (!is_file($fullPath)) {
            return $url;
        }

        $version = filemtime($fullPath);
        if ($version === false) {
            return $url;
        }

        return $url . '?v=' . $version;
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }

        return (string) $_SESSION['_csrf'];
    }
}

if (!function_exists('csrf_validate')) {
    function csrf_validate(?string $token): bool
    {
        if (!is_string($token) || $token === '') {
            return false;
        }

        $sessionToken = $_SESSION['_csrf'] ?? '';
        return is_string($sessionToken) && hash_equals($sessionToken, $token);
    }
}

if (!function_exists('format_bytes')) {
    function format_bytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }

        $units = ['KB', 'MB', 'GB'];
        $value = (float) $bytes;
        $unitIndex = -1;

        do {
            $value /= 1024;
            ++$unitIndex;
        } while ($value >= 1024 && $unitIndex < count($units) - 1);

        $rounded = $value >= 100 || $value === floor($value)
            ? (string) (int) round($value)
            : rtrim(rtrim(number_format($value, 1, '.', ''), '0'), '.');

        return $rounded . ' ' . $units[$unitIndex];
    }
}

if (!function_exists('flash')) {
    function flash(string $key, ?string $message = null): ?string
    {
        if ($message !== null) {
            $_SESSION['_flash'][$key] = $message;
            return null;
        }

        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return is_string($value) ? $value : null;
    }
}
