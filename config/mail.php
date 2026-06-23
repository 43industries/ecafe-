<?php

declare(strict_types=1);

return [
    'host' => env('MAIL_HOST', ''),
    'port' => (int) env('MAIL_PORT', 587),
    'username' => env('MAIL_USERNAME', ''),
    'password' => env('MAIL_PASSWORD', ''),
    'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@schoolcafe.local'),
    'from_name' => env('MAIL_FROM_NAME', 'School e-Café'),
];
