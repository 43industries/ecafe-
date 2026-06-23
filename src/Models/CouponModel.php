<?php

declare(strict_types=1);

namespace App\Models;

class CouponModel extends BaseModel
{
    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM coupons WHERE code = ? AND is_active = 1
             AND (starts_at IS NULL OR starts_at <= NOW())
             AND (expires_at IS NULL OR expires_at >= NOW()) LIMIT 1"
        );
        $stmt->execute([strtoupper($code)]);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM coupons ORDER BY created_at DESC')->fetchAll();
    }

    public function redeem(int $couponId, int $studentId, int $orderId, float $discount): void
    {
        $this->db->prepare(
            'INSERT INTO coupon_redemptions (coupon_id, student_id, order_id, discount_applied) VALUES (?,?,?,?)'
        )->execute([$couponId, $studentId, $orderId, $discount]);
        $this->db->prepare('UPDATE coupons SET times_used = times_used + 1 WHERE id = ?')->execute([$couponId]);
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO coupons (code, description, discount_type, discount_value, min_order_amount, usage_limit, starts_at, expires_at)
             VALUES (?,?,?,?,?,?,?,?)'
        );
        $stmt->execute([
            strtoupper($data['code']), $data['description'] ?? null,
            $data['discount_type'], $data['discount_value'], $data['min_order_amount'] ?? 0,
            $data['usage_limit'] ?? null, $data['starts_at'] ?? null, $data['expires_at'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
