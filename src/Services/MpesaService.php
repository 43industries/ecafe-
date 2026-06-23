<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use App\Models\PaymentModel;

class MpesaService
{
    private array $config;
    private ?string $cachedToken = null;
    private ?int $tokenExpiry = null;
    private PaymentModel $payments;

    public function __construct()
    {
        $this->config = require ECAFE_ROOT . '/config/mpesa.php';
        $this->payments = new PaymentModel();
    }

    public function initiateStkPush(int $paymentId, string $phone, float $amount, string $orderNumber): array
    {
        $phone = $this->formatPhone($phone);
        if (!$phone) {
            return ['success' => false, 'message' => 'Invalid phone number. Use format 2547XXXXXXXX.'];
        }

        if (empty($this->config['consumer_key']) || empty($this->config['consumer_secret'])) {
            return ['success' => false, 'message' => 'M-Pesa is not configured. Add credentials to .env'];
        }

        try {
            $token = $this->getAccessToken();
            $timestamp = date('YmdHis');
            $password = base64_encode($this->config['shortcode'] . $this->config['passkey'] . $timestamp);

            $client = new Client(['base_uri' => $this->config['base_url']]);
            $response = $client->post('/mpesa/stkpush/v1/processrequest', [
                'headers' => [
                    'Authorization' => "Bearer {$token}",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'BusinessShortCode' => $this->config['shortcode'],
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'TransactionType' => 'CustomerPayBillOnline',
                    'Amount' => (int) ceil($amount),
                    'PartyA' => $phone,
                    'PartyB' => $this->config['shortcode'],
                    'PhoneNumber' => $phone,
                    'CallBackURL' => $this->config['callback_url'],
                    'AccountReference' => $orderNumber,
                    'TransactionDesc' => "Order {$orderNumber}",
                ],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);
            $checkoutId = $body['CheckoutRequestID'] ?? null;

            if ($checkoutId) {
                $this->payments->setCheckoutId($paymentId, $checkoutId);
                return ['success' => true, 'checkout_id' => $checkoutId, 'message' => 'STK push sent. Check your phone.'];
            }

            return ['success' => false, 'message' => $body['errorMessage'] ?? 'STK push failed.'];
        } catch (\Throwable $e) {
            $this->log('STK Error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Payment service unavailable. Try again later.'];
        }
    }

    public function handleCallback(array $payload): void
    {
        $this->log('Callback: ' . json_encode($payload));

        $checkoutId = $payload['Body']['stkCallback']['CheckoutRequestID'] ?? null;
        $resultCode = $payload['Body']['stkCallback']['ResultCode'] ?? -1;

        if (!$checkoutId) {
            return;
        }

        $payment = $this->payments->findByCheckoutId($checkoutId);
        if (!$payment) {
            return;
        }

        if ((int) $resultCode === 0) {
            $items = $payload['Body']['stkCallback']['CallbackMetadata']['Item'] ?? [];
            $receipt = '';
            foreach ($items as $item) {
                if (($item['Name'] ?? '') === 'MpesaReceiptNumber') {
                    $receipt = $item['Value'] ?? '';
                }
            }
            $this->payments->updateStatus((int) $payment['id'], 'paid', $receipt);
        } else {
            $this->payments->updateStatus((int) $payment['id'], 'failed');
        }
    }

    private function getAccessToken(): string
    {
        if ($this->cachedToken && $this->tokenExpiry && time() < $this->tokenExpiry) {
            return $this->cachedToken;
        }

        $client = new Client(['base_uri' => $this->config['base_url']]);
        $response = $client->get('/oauth/v1/generate?grant_type=client_credentials', [
            'auth' => [$this->config['consumer_key'], $this->config['consumer_secret']],
        ]);

        $body = json_decode($response->getBody()->getContents(), true);
        $this->cachedToken = $body['access_token'] ?? '';
        $this->tokenExpiry = time() + 3300;
        return $this->cachedToken;
    }

    private function formatPhone(string $phone): ?string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '254' . substr($phone, 1);
        }
        if (str_starts_with($phone, '7') && strlen($phone) === 9) {
            $phone = '254' . $phone;
        }
        return strlen($phone) === 12 && str_starts_with($phone, '254') ? $phone : null;
    }

    private function log(string $message): void
    {
        file_put_contents(ECAFE_ROOT . '/storage/logs/mpesa.log', date('c') . ' ' . $message . "\n", FILE_APPEND);
    }
}
