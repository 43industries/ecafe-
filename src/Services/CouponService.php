<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CouponModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\StudentModel;

class CouponService
{
    private CouponModel $coupons;

    public function __construct()
    {
        $this->coupons = new CouponModel();
    }

    public function validate(string $code, float $subtotal, int $studentId): array
    {
        $coupon = $this->coupons->findByCode($code);
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid or expired coupon.'];
        }
        if ($coupon['usage_limit'] && $coupon['times_used'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'Coupon usage limit reached.'];
        }
        if ($subtotal < (float) $coupon['min_order_amount']) {
            return ['valid' => false, 'message' => 'Order does not meet minimum amount for this coupon.'];
        }

        $discount = $coupon['discount_type'] === 'percentage'
            ? $subtotal * ((float) $coupon['discount_value'] / 100)
            : (float) $coupon['discount_value'];

        $discount = min($discount, $subtotal);

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => round($discount, 2),
        ];
    }

    public function redeem(int $couponId, int $studentId, int $orderId, float $discount): void
    {
        $this->coupons->redeem($couponId, $studentId, $orderId, $discount);
    }
}
