<?php

declare(strict_types=1);

$env = env('MPESA_ENV', 'sandbox');

return [
    'consumer_key' => env('MPESA_CONSUMER_KEY', ''),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET', ''),
    'passkey' => env('MPESA_PASSKEY', ''),
    'shortcode' => env('MPESA_SHORTCODE', '174379'),
    'env' => $env,
    'callback_url' => env('MPESA_CALLBACK_URL', ''),
    'base_url' => $env === 'production'
        ? 'https://api.safaricom.co.ke'
        : 'https://sandbox.safaricom.co.ke',
];
