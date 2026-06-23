<?php

declare(strict_types=1);

namespace App\Models;

class MenuModel extends BaseModel
{
    public function getCategories(bool $activeOnly = true): array
    {
        $sql = 'SELECT * FROM categories';
        if ($activeOnly) {
            $sql .= ' WHERE is_active = 1';
        }
        $sql .= ' ORDER BY sort_order, name';
        return $this->db->query($sql)->fetchAll();
    }

    public function search(?string $query, ?int $categoryId, int $limit, int $offset): array
    {
        $sql = 'SELECT m.*, c.name AS category_name, i.quantity AS stock
                FROM menu_items m
                JOIN categories c ON c.id = m.category_id
                LEFT JOIN inventory i ON i.menu_item_id = m.id
                WHERE m.is_available = 1';
        $params = [];

        if ($categoryId) {
            $sql .= ' AND m.category_id = ?';
            $params[] = $categoryId;
        }
        if ($query) {
            $sql .= ' AND (m.name LIKE ? OR m.description LIKE ?)';
            $params[] = "%{$query}%";
            $params[] = "%{$query}%";
        }

        $sql .= ' ORDER BY m.is_special DESC, m.name LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $i => $param) {
            $type = is_int($param) ? \PDO::PARAM_INT : \PDO::PARAM_STR;
            $stmt->bindValue($i + 1, $param, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countSearch(?string $query, ?int $categoryId): int
    {
        $sql = 'SELECT COUNT(*) FROM menu_items m WHERE m.is_available = 1';
        $params = [];
        if ($categoryId) {
            $sql .= ' AND m.category_id = ?';
            $params[] = $categoryId;
        }
        if ($query) {
            $sql .= ' AND (m.name LIKE ? OR m.description LIKE ?)';
            $params[] = "%{$query}%";
            $params[] = "%{$query}%";
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, c.name AS category_name, i.quantity AS stock
             FROM menu_items m
             JOIN categories c ON c.id = m.category_id
             LEFT JOIN inventory i ON i.menu_item_id = m.id
             WHERE m.id = ?'
        );
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getSpecials(int $limit = 6): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, c.name AS category_name FROM menu_items m
             JOIN categories c ON c.id = m.category_id
             WHERE m.is_special = 1 AND m.is_available = 1
             ORDER BY m.updated_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getPopular(int $limit = 5): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.*, SUM(oi.quantity) AS order_count
             FROM order_items oi
             JOIN menu_items m ON m.id = oi.menu_item_id
             GROUP BY m.id
             ORDER BY order_count DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO menu_items (category_id, name, slug, description, price, image, is_available, is_special, is_featured, prep_time_minutes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['category_id'], $data['name'], $data['slug'], $data['description'] ?? null,
            $data['price'], $data['image'] ?? null, $data['is_available'] ?? 1,
            $data['is_special'] ?? 0, $data['is_featured'] ?? 0, $data['prep_time_minutes'] ?? 10,
        ]);
        $id = (int) $this->db->lastInsertId();
        $this->db->prepare('INSERT INTO inventory (menu_item_id, quantity) VALUES (?, ?)')
            ->execute([$id, $data['stock'] ?? 50]);
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE menu_items SET category_id=?, name=?, slug=?, description=?, price=?, image=?, is_available=?, is_special=?, is_featured=?, prep_time_minutes=? WHERE id=?'
        );
        return $stmt->execute([
            $data['category_id'], $data['name'], $data['slug'], $data['description'] ?? null,
            $data['price'], $data['image'] ?? null, $data['is_available'] ?? 1,
            $data['is_special'] ?? 0, $data['is_featured'] ?? 0, $data['prep_time_minutes'] ?? 10, $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE menu_items SET is_available = 0 WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
