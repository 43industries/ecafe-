<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\NotificationModel;

class NotificationService
{
    private NotificationModel $notifications;

    public function __construct()
    {
        $this->notifications = new NotificationModel();
    }

    public function notify(int $studentId, string $title, string $message, string $type = 'info', ?string $link = null): void
    {
        $this->notifications->create($studentId, $title, $message, $type, $link);
        $this->sendSmsIfEnabled($studentId, $message);
    }

    public function orderPlaced(int $studentId, string $orderNumber): void
    {
        $this->notify(
            $studentId,
            'Order Placed',
            "Your order {$orderNumber} has been placed successfully.",
            'order',
            url('/student/orders')
        );
    }

    public function orderReady(int $studentId, string $orderNumber): void
    {
        $this->notify(
            $studentId,
            'Order Ready!',
            "Your order {$orderNumber} is ready for pickup.",
            'success',
            url('/student/orders')
        );
    }

    public function orderStatusChanged(int $studentId, string $orderNumber, string $status): void
    {
        $this->notify(
            $studentId,
            'Order Update',
            "Order {$orderNumber} status: " . ucfirst($status),
            'order',
            url('/student/orders')
        );
    }

    private function sendSmsIfEnabled(int $studentId, string $message): void
    {
        if (!filter_var(env('SMS_ENABLED', false), FILTER_VALIDATE_BOOLEAN)) {
            return;
        }
        // Optional SMS integration stub
        $logFile = ECAFE_ROOT . '/storage/logs/sms.log';
        file_put_contents($logFile, date('c') . " [student:{$studentId}] {$message}\n", FILE_APPEND);
    }
}
