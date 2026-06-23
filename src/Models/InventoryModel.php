<?php

declare(strict_types=1);

namespace App\Models;

class InventoryModel extends BaseModel
{
    public function all(): array
    {
        return $this->db->query(
            'SELECT i.*, m.name AS item_name, m.price FROM inventory i JOIN menu_items m ON m.id = i.menu_item_id ORDER BY m.name'
        )->fetchAll();
    }

    public function updateQuantity(int $menuItemId, int $quantity): void
    {
        $stmt = $this->db->prepare(
            'UPDATE inventory SET quantity = ?, last_restocked_at = NOW() WHERE menu_item_id = ?'
        );
        $stmt->execute([$quantity, $menuItemId]);
    }

    public function decrement(int $menuItemId, int $qty): void
    {
        $stmt = $this->db->prepare(
            'UPDATE inventory SET quantity = GREATEST(0, quantity - ?) WHERE menu_item_id = ?'
        );
        $stmt->execute([$qty, $menuItemId]);
    }

    public function getLowStock(): array
    {
        return $this->db->query(
            'SELECT i.*, m.name FROM inventory i JOIN menu_items m ON m.id = i.menu_item_id
             WHERE i.quantity <= i.low_stock_threshold ORDER BY i.quantity'
        )->fetchAll();
    }
}
