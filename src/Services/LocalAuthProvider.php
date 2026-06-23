<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\Database;
use App\Services\Contracts\AuthProviderInterface;
use PDO;

class LocalAuthProvider implements AuthProviderInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function authenticate(string $identifier, string $secret): ?array
    {
        $identifier = trim($identifier);

        // Try student by student_id
        $student = $this->findUser('students', 'student_id', $identifier);
        if ($student && password_verify($secret, $student['password_hash'])) {
            return $this->formatUser($student, 'student', $student['student_id']);
        }

        // Try staff/admin by username
        $staff = $this->findUser('staff', 'username', $identifier);
        if ($staff && password_verify($secret, $staff['password_hash'])) {
            return $this->formatUser($staff, 'staff', $staff['username']);
        }

        $admin = $this->findUser('admins', 'username', $identifier);
        if ($admin && password_verify($secret, $admin['password_hash'])) {
            return $this->formatUser($admin, 'admin', $admin['username']);
        }

        // Try student by email
        $studentEmail = $this->findUser('students', 'email', $identifier);
        if ($studentEmail && password_verify($secret, $studentEmail['password_hash'])) {
            return $this->formatUser($studentEmail, 'student', $studentEmail['student_id']);
        }

        return null;
    }

    public function getProviderName(): string
    {
        return 'local';
    }

    private function findUser(string $table, string $column, string $value): ?array
    {
        $allowed = ['students', 'staff', 'admins'];
        if (!in_array($table, $allowed, true)) {
            return null;
        }

        $stmt = $this->db->prepare("SELECT * FROM {$table} WHERE {$column} = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$value]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    private function formatUser(array $user, string $role, string $identifier): array
    {
        $this->updateLastLogin($role, (int) $user['id']);

        return [
            'id' => (int) $user['id'],
            'role' => $role,
            'name' => $user['full_name'],
            'identifier' => $identifier,
            'email' => $user['email'] ?? '',
        ];
    }

    private function updateLastLogin(string $role, int $id): void
    {
        $table = match ($role) {
            'student' => 'students',
            'staff' => 'staff',
            'admin' => 'admins',
            default => null,
        };
        if (!$table) {
            return;
        }
        $stmt = $this->db->prepare("UPDATE {$table} SET last_login_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);
    }
}
