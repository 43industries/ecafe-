<?php

declare(strict_types=1);

namespace App\Models;

class FavoriteModel extends BaseModel
{
    public function toggle(int $studentId, int $menuItemId): bool
    {
        $stmt = $this->db->prepare('SELECT id FROM favorites WHERE student_id = ? AND menu_item_id = ?');
        $stmt->execute([$studentId, $menuItemId]);
        if ($stmt->fetch()) {
            $this->db->prepare('DELETE FROM favorites WHERE student_id = ? AND menu_item_id = ?')
                ->execute([$studentId, $menuItemId]);
            return false;
        }
        $this->db->prepare('INSERT INTO favorites (student_id, menu_item_id) VALUES (?, ?)')
            ->execute([$studentId, $menuItemId]);
        return true;
    }

    public function getByStudent(int $studentId): array
    {
        $stmt = $this->db->prepare(
            'SELECT m.* FROM favorites f JOIN menu_items m ON m.id = f.menu_item_id WHERE f.student_id = ?'
        );
        $stmt->execute([$studentId]);
        return $stmt->fetchAll();
    }

    public function isFavorite(int $studentId, int $menuItemId): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM favorites WHERE student_id = ? AND menu_item_id = ?');
        $stmt->execute([$studentId, $menuItemId]);
        return (bool) $stmt->fetch();
    }

    public function getFavoriteIds(int $studentId): array
    {
        $stmt = $this->db->prepare('SELECT menu_item_id FROM favorites WHERE student_id = ?');
        $stmt->execute([$studentId]);
        return array_column($stmt->fetchAll(), 'menu_item_id');
    }
}
