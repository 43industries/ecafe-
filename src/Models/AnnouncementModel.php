<?php

declare(strict_types=1);

namespace App\Models;

class AnnouncementModel extends BaseModel
{
    public function getActive(string $role = 'all'): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM announcements WHERE is_active = 1
             AND (target_role = 'all' OR target_role = ?)
             AND (starts_at IS NULL OR starts_at <= NOW())
             AND (ends_at IS NULL OR ends_at >= NOW())
             ORDER BY created_at DESC"
        );
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM announcements ORDER BY created_at DESC')->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO announcements (title, content, image, target_role, is_active, starts_at, ends_at) VALUES (?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            $data['title'], $data['content'], $data['image'] ?? null,
            $data['target_role'] ?? 'all', $data['is_active'] ?? 1,
            $data['starts_at'] ?? null, $data['ends_at'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE announcements SET title=?, content=?, image=?, target_role=?, is_active=?, starts_at=?, ends_at=? WHERE id=?'
        );
        return $stmt->execute([
            $data['title'], $data['content'], $data['image'] ?? null,
            $data['target_role'] ?? 'all', $data['is_active'] ?? 1,
            $data['starts_at'] ?? null, $data['ends_at'] ?? null, $id,
        ]);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare('DELETE FROM announcements WHERE id = ?')->execute([$id]);
    }
}
