<?php

declare(strict_types=1);

namespace App\Models;

class CartModel extends BaseModel
{
    public function getItems(int $studentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT c.*, m.name, m.price, m.image, m.is_available, i.quantity AS stock
             FROM cart c
             JOIN menu_items m ON m.id = c.menu_item_id
             LEFT JOIN inventory i ON i.menu_item_id = m.id
             WHERE c.student_id = ?'
        );
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function addOrUpdate(int $studentId, int $menuItemId, int $quantity): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO cart (student_id, menu_item_id, quantity) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)'
        );
        $stmt->execute([$studentId, $menuItemId, $quantity]);
    }

    public function setQuantity(int $studentId, int $menuItemId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($studentId, $menuItemId);
            return;
        }
        $stmt = $this->db->prepare(
            'UPDATE cart SET quantity = ? WHERE student_id = ? AND menu_item_id = ?'
        );
        $stmt->execute([$quantity, $studentId, $menuItemId]);
    }

    public function remove(int $studentId, int $menuItemId): void
    {
        $stmt = $this->db->prepare('DELETE FROM cart WHERE student_id = ? AND menu_item_id = ?');
        $stmt->execute([$studentId, $menuItemId]);
    }

    public function clear(int $studentId): void
    {
        $stmt = $this->db->prepare('DELETE FROM cart WHERE student_id = ?');
        $stmt->execute([$studentId]);
    }

    public function getTotal(int $studentId): float
    {
        $stmt = $this->db->prepare(
            'SELECT COALESCE(SUM(c.quantity * m.price), 0) FROM cart c
             JOIN menu_items m ON m.id = c.menu_item_id WHERE c.student_id = ?'
        );
        $stmt->execute([$studentId]);
        return (float) $stmt->fetchColumn();
    }

    public function countItems(int $studentId): int
    {
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(quantity),0) FROM cart WHERE student_id = ?');
        $stmt->execute([$studentId]);
        return (int) $stmt->fetchColumn();
    }
}
