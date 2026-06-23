<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use App\Models\StudentModel;

class MailService
{
    private array $config;

    public function __construct()
    {
        $this->config = require ECAFE_ROOT . '/config/mail.php';
    }

    public function sendOrderConfirmation(int $studentId, string $orderNumber, float $total): void
    {
        $student = (new StudentModel())->findById($studentId);
        if (!$student || empty($student['email'])) {
            return;
        }

        $subject = "Order Confirmation - {$orderNumber}";
        $body = "<h2>Thank you for your order!</h2>
                 <p>Order <strong>{$orderNumber}</strong> has been placed.</p>
                 <p>Total: KES " . number_format($total, 2) . "</p>
                 <p>We'll notify you when it's ready for pickup.</p>";

        $this->send($student['email'], $subject, $body);
    }

    public function sendOrderReady(int $studentId, string $orderNumber): void
    {
        $student = (new StudentModel())->findById($studentId);
        if (!$student || empty($student['email'])) {
            return;
        }

        $subject = "Your Order is Ready - {$orderNumber}";
        $body = "<h2>Your order is ready!</h2>
                 <p>Order <strong>{$orderNumber}</strong> is ready for pickup at the school café.</p>
                 <p>Please bring your order number or QR code.</p>";

        $this->send($student['email'], $subject, $body);
    }

    private function send(string $to, string $subject, string $body): void
    {
        if (empty($this->config['host'])) {
            file_put_contents(
                ECAFE_ROOT . '/storage/logs/mail.log',
                date('c') . " To: {$to} | {$subject}\n",
                FILE_APPEND
            );
            return;
        }

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $this->config['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $this->config['username'];
            $mail->Password = $this->config['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $this->config['port'];
            $mail->setFrom($this->config['from_address'], $this->config['from_name']);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->send();
        } catch (\Throwable $e) {
            file_put_contents(ECAFE_ROOT . '/storage/logs/mail.log', date('c') . ' Error: ' . $e->getMessage() . "\n", FILE_APPEND);
        }
    }
}
