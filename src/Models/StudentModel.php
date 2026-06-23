<?php

declare(strict_types=1);

namespace App\Models;

class StudentModel extends BaseModel
{
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findByStudentId(string $studentId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM students WHERE student_id = ? LIMIT 1');
        $stmt->execute([$studentId]);
        return $stmt->fetch() ?: null;
    }

    public function all(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare('SELECT * FROM students ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM students')->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO students (student_id, email, password_hash, full_name, phone, grade)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['student_id'],
            $data['email'],
            password_hash($data['password'], PASSWORD_DEFAULT),
            $data['full_name'],
            $data['phone'] ?? null,
            $data['grade'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $fields = [];
        $values = [];
        foreach (['full_name', 'email', 'phone', 'grade', 'avatar'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "{$field} = ?";
                $values[] = $data[$field];
            }
        }
        if (isset($data['password']) && $data['password']) {
            $fields[] = 'password_hash = ?';
            $values[] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (empty($fields)) {
            return false;
        }
        $values[] = $id;
        $sql = 'UPDATE students SET ' . implode(', ', $fields) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($values);
    }

    public function updateLoyaltyPoints(int $id, int $points): void
    {
        $stmt = $this->db->prepare('UPDATE students SET loyalty_points = loyalty_points + ? WHERE id = ?');
        $stmt->execute([$points, $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE students SET is_active = 0 WHERE id = ?');
        return $stmt->execute([$id]);
    }
}
