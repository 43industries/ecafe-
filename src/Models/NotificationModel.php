<?php

declare(strict_types=1);

namespace App\Models;

class NotificationModel extends BaseModel
{
    public function create(int $studentId, string $title, string $message, string $type = 'info', ?string $link = null): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO notifications (student_id, type, title, message, link) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$studentId, $type, $title, $message, $link]);
        return (int) $this->db->lastInsertId();
    }

    public function getByStudent(int $studentId, int $limit = 20): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM notifications WHERE student_id = ? ORDER BY created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $studentId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function unreadCount(int $studentId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM notifications WHERE student_id = ? AND is_read = 0');
        $stmt->execute([$studentId]);
        return (int) $stmt->fetchColumn();
    }

    public function markRead(int $id, int $studentId): void
    {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE id = ? AND student_id = ?');
        $stmt->execute([$id, $studentId]);
    }

    public function markAllRead(int $studentId): void
    {
        $stmt = $this->db->prepare('UPDATE notifications SET is_read = 1 WHERE student_id = ?');
        $stmt->execute([$studentId]);
    }
}
