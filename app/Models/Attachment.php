<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Attachment
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
            'INSERT INTO attachments (paste_id, original_name, stored_name, mime_type, size_bytes, storage_path, created_at)
             VALUES (:paste_id, :original_name, :stored_name, :mime_type, :size_bytes, :storage_path, :created_at)'
        );

        $stmt->execute([
            ':paste_id' => $data['paste_id'],
            ':original_name' => $data['original_name'],
            ':stored_name' => $data['stored_name'],
            ':mime_type' => $data['mime_type'],
            ':size_bytes' => $data['size_bytes'],
            ':storage_path' => $data['storage_path'],
            ':created_at' => $data['created_at'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /** @return array<int, array<string, mixed>> */
    public function findByPasteId(int $pasteId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM attachments WHERE paste_id = :paste_id ORDER BY id ASC');
        $stmt->execute([':paste_id' => $pasteId]);
        $rows = $stmt->fetchAll();
        return is_array($rows) ? $rows : [];
    }

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, p.short_code, p.expires_at
             FROM attachments a
             INNER JOIN pastes p ON p.id = a.paste_id
             WHERE a.id = :id
             LIMIT 1'
        );
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch();
        return is_array($result) ? $result : null;
    }
}
