<?php

declare(strict_types=1);

namespace App\Models;

class CategoryModel extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT * FROM categories ORDER BY sort_order, name')->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM categories WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO categories (name, slug, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['name'], $data['slug'], $data['description'] ?? null,
            $data['sort_order'] ?? 0, $data['is_active'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE categories SET name=?, slug=?, description=?, sort_order=?, is_active=? WHERE id=?'
        );
        return $stmt->execute([
            $data['name'], $data['slug'], $data['description'] ?? null,
            $data['sort_order'] ?? 0, $data['is_active'] ?? 1, $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE categories SET is_active = 0 WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
