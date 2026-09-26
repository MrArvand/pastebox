<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Paste
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO pastes (uuid, short_code, content, password_hash, burn_after_reading, expires_at, created_at)
             VALUES (:uuid, :short_code, :content, :password_hash, :burn_after_reading, :expires_at, :created_at)'
        );
        $stmt->execute([
            ':uuid' => $data['uuid'],
            ':short_code' => $data['short_code'],
            ':content' => $data['content'],
            ':password_hash' => $data['password_hash'],
            ':burn_after_reading' => $data['burn_after_reading'],
            ':expires_at' => $data['expires_at'],
            ':created_at' => $data['created_at'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @return array<string, mixed>|null */
    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM pastes WHERE short_code = :code LIMIT 1');
        $stmt->execute([':code' => $code]);
        $result = $stmt->fetch();
        return is_array($result) ? $result : null;
    }

    public function existsByCode(string $code): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM pastes WHERE short_code = :code LIMIT 1');
        $stmt->execute([':code' => $code]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Highest id among rows still present (fallback when counter row missing / drift).
     */
    public function maxRowId(): int
    {
        $value = $this->db->query('SELECT COALESCE(MAX(`id`), 0) FROM pastes')->fetchColumn();
        return max(0, (int) $value);
    }

    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    public function commit(): void
    {
        $this->db->commit();
    }

    public function rollBack(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollBack();
        }
    }

    /** @return array<int, array<string, mixed>> */
    public function expiredWithAttachments(\DateTimeImmutable $now): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.id as paste_id, a.id as attachment_id, a.storage_path
             FROM pastes p
             LEFT JOIN attachments a ON a.paste_id = p.id
             WHERE p.expires_at <= :now'
        );
        $stmt->execute([':now' => $now->format('Y-m-d H:i:s')]);
        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    public function deleteExpired(\DateTimeImmutable $now): int
    {
        $stmt = $this->db->prepare('DELETE FROM pastes WHERE expires_at <= :now');
        $stmt->execute([':now' => $now->format('Y-m-d H:i:s')]);
        return $stmt->rowCount();
    }

    public function deleteById(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM pastes WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
}
