<?php

declare(strict_types=1);

namespace App\Models;

class StaffModel extends BaseModel
{
    public function all(): array
    {
        return $this->db->query('SELECT id, username, email, full_name, phone, is_active, last_login_at FROM staff ORDER BY full_name')->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO staff (username, email, password_hash, full_name, phone) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['username'], $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['full_name'], $data['phone'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = 'username=?, email=?, full_name=?, phone=?, is_active=?';
        $params = [$data['username'], $data['email'], $data['full_name'], $data['phone'] ?? null, $data['is_active'] ?? 1];
        if (!empty($data['password'])) {
            $fields .= ', password_hash=?';
            $params[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $params[] = $id;
        return $this->db->prepare("UPDATE staff SET {$fields} WHERE id=?")->execute($params);
    }

    public function delete(int $id): bool
    {
        return $this->db->prepare('UPDATE staff SET is_active = 0 WHERE id = ?')->execute([$id]);
    }
}
