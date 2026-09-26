<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class AppCounter
{
    private const KEY_PASTES_CREATED_TOTAL = 'pastes_created_total';

    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /** Increment inside the same transaction as paste creation (rolls back with failed commits). */
    public function incrementPastesCreated(string $counterKey = self::KEY_PASTES_CREATED_TOTAL): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO app_counters (counter_key, counter_value)
             VALUES (:key, 1)
             ON DUPLICATE KEY UPDATE counter_value = counter_value + 1'
        );
        $stmt->execute([':key' => $counterKey]);
    }

    public function pastesCreatedTotal(string $counterKey = self::KEY_PASTES_CREATED_TOTAL): int
    {
        $stmt = $this->db->prepare(
            'SELECT counter_value FROM app_counters WHERE counter_key = :key LIMIT 1'
        );
        $stmt->execute([':key' => $counterKey]);
        $value = $stmt->fetchColumn();
        return max(0, (int) $value);
    }
}
