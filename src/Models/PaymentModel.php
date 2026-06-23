<?php

declare(strict_types=1);

namespace App\Models;

class PaymentModel extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO payments (order_id, method, amount, status, mpesa_phone, transaction_ref)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['order_id'], $data['method'], $data['amount'],
            $data['status'] ?? 'pending', $data['mpesa_phone'] ?? null, $data['transaction_ref'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findByCheckoutId(string $checkoutId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE mpesa_checkout_id = ? LIMIT 1');
        $stmt->execute([$checkoutId]);
        return $stmt->fetch() ?: null;
    }

    public function updateStatus(int $id, string $status, ?string $receipt = null): void
    {
        $sql = 'UPDATE payments SET status = ?';
        $params = [$status];
        if ($receipt) {
            $sql .= ', mpesa_receipt = ?, paid_at = NOW()';
            $params[] = $receipt;
        }
        $sql .= ' WHERE id = ?';
        $params[] = $id;
        $this->db->prepare($sql)->execute($params);
    }

    public function setCheckoutId(int $id, string $checkoutId): void
    {
        $stmt = $this->db->prepare('UPDATE payments SET mpesa_checkout_id = ? WHERE id = ?');
        $stmt->execute([$checkoutId, $id]);
    }

    public function all(int $limit = 50): array
    {
        $stmt = $this->db->prepare(
            'SELECT p.*, o.order_number FROM payments p JOIN orders o ON o.id = p.order_id ORDER BY p.created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
