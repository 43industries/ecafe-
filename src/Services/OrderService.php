<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\InventoryModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;

class OrderService
{
    private OrderModel $orders;
    private CartService $cart;
    private PaymentModel $payments;
    private InventoryModel $inventory;
    private NotificationService $notifications;
    private LoyaltyService $loyalty;
    private CouponService $coupons;
    private MailService $mail;

    public function __construct()
    {
        $this->orders = new OrderModel();
        $this->cart = new CartService();
        $this->payments = new PaymentModel();
        $this->inventory = new InventoryModel();
        $this->notifications = new NotificationService();
        $this->loyalty = new LoyaltyService();
        $this->coupons = new CouponService();
        $this->mail = new MailService();
    }

    public function checkout(int $studentId, array $data): array
    {
        $cartItems = $this->cart->getCart($studentId);
        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Your cart is empty.'];
        }

        $subtotal = $this->cart->getTotal($studentId);
        $discount = 0;
        $couponId = null;
        $couponDiscount = 0;
        $loyaltyUsed = (int) ($data['loyalty_points'] ?? 0);

        if (!empty($data['coupon_code'])) {
            $couponResult = $this->coupons->validate($data['coupon_code'], $subtotal, $studentId);
            if ($couponResult['valid']) {
                $couponDiscount = $couponResult['discount'];
                $discount += $couponDiscount;
                $couponId = $couponResult['coupon']['id'];
            }
        }

        if ($loyaltyUsed > 0) {
            $loyaltyDiscount = $this->loyalty->redeemPoints($studentId, $loyaltyUsed);
            $discount += $loyaltyDiscount;
        }

        $total = max(0, $subtotal - $discount);
        $orderNumber = $this->orders->generateOrderNumber();

        $orderItems = array_map(fn($item) => [
            'menu_item_id' => (int) $item['menu_item_id'],
            'item_name' => $item['name'],
            'quantity' => (int) $item['quantity'],
            'unit_price' => (float) $item['price'],
            'subtotal' => (float) $item['price'] * (int) $item['quantity'],
        ], $cartItems);

        $orderId = $this->orders->create([
            'student_id' => $studentId,
            'order_number' => $orderNumber,
            'status' => 'pending',
            'subtotal' => $subtotal,
            'discount_amount' => $discount,
            'total_amount' => $total,
            'loyalty_points_used' => $loyaltyUsed,
            'pickup_time' => $data['pickup_time'],
            'notes' => $data['notes'] ?? null,
            'coupon_id' => $couponId,
        ], $orderItems);

        if ($couponId) {
            $this->coupons->redeem($couponId, $studentId, $orderId, $couponDiscount);
        }

        foreach ($cartItems as $item) {
            $this->inventory->decrement((int) $item['menu_item_id'], (int) $item['quantity']);
        }

        $paymentId = $this->payments->create([
            'order_id' => $orderId,
            'method' => $data['payment_method'],
            'amount' => $total,
            'status' => $data['payment_method'] === 'cash' ? 'pending' : 'pending',
            'mpesa_phone' => $data['mpesa_phone'] ?? null,
        ]);

        $this->cart->clear($studentId);
        $this->notifications->orderPlaced($studentId, $orderNumber);
        $this->mail->sendOrderConfirmation($studentId, $orderNumber, $total);

        return [
            'success' => true,
            'order_id' => $orderId,
            'order_number' => $orderNumber,
            'payment_id' => $paymentId,
            'total' => $total,
        ];
    }

    public function updateStatus(int $orderId, string $status): bool
    {
        $order = $this->orders->findById($orderId);
        if (!$order) {
            return false;
        }

        $this->orders->updateStatus($orderId, $status);

        if ($status === 'ready') {
            $this->generateQrCode($orderId, $order['order_number']);
            $this->notifications->orderReady((int) $order['student_id'], $order['order_number']);
            $this->mail->sendOrderReady((int) $order['student_id'], $order['order_number']);
        } elseif ($status === 'completed') {
            $this->loyalty->awardPoints((int) $order['student_id'], (float) $order['total_amount']);
            $payment = $order['payment'];
            if ($payment && $payment['method'] === 'cash' && $payment['status'] === 'pending') {
                $this->payments->updateStatus((int) $payment['id'], 'paid');
            }
        } else {
            $this->notifications->orderStatusChanged(
                (int) $order['student_id'],
                $order['order_number'],
                $status
            );
        }

        return true;
    }

    public function generateQrCode(int $orderId, string $orderNumber): string
    {
        $hash = substr(hash('sha256', $orderNumber . env('APP_NAME', 'ecafe')), 0, 16);
        $payload = json_encode(['order' => $orderNumber, 'hash' => $hash]);

        $dir = ECAFE_ROOT . '/public/assets/img/qr';
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = "qr_{$orderId}.png";
        $path = "{$dir}/{$filename}";

        if (class_exists(Builder::class)) {
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($payload)
                ->size(200)
                ->margin(10)
                ->build();
            $result->saveToFile($path);
        } else {
            // Fallback without composer
            file_put_contents($path, '');
        }

        $relativePath = "assets/img/qr/{$filename}";
        $this->orders->setQrCode($orderId, $relativePath);
        return $relativePath;
    }
}
