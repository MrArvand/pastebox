<?php

declare(strict_types=1);

namespace App\Services;

final class RateLimiter
{
    public function hit(string $key, int $maxAttempts, int $windowSeconds): bool
    {
        $now = time();
        $bucket = $_SESSION['_ratelimit'][$key] ?? ['count' => 0, 'expires_at' => $now + $windowSeconds];

        if (($bucket['expires_at'] ?? 0) < $now) {
            $bucket = ['count' => 0, 'expires_at' => $now + $windowSeconds];
        }

        $bucket['count'] = (int) ($bucket['count'] ?? 0) + 1;
        $_SESSION['_ratelimit'][$key] = $bucket;

        return $bucket['count'] <= $maxAttempts;
    }
}
