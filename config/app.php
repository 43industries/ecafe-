<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'School e-Café'),
    'url' => rtrim(env('APP_URL', 'http://localhost/ecafe/public'), '/'),
    'env' => env('APP_ENV', 'local'),
    'debug' => filter_var(env('APP_DEBUG', true), FILTER_VALIDATE_BOOLEAN),
    'timezone' => env('APP_TIMEZONE', 'Africa/Nairobi'),
    'session_lifetime' => (int) env('SESSION_LIFETIME', 1800),
    'upload_max_size' => 2 * 1024 * 1024,
    'allowed_image_types' => ['image/jpeg', 'image/png', 'image/webp'],
    'currency' => 'KES',
    'loyalty_points_per_unit' => 1,
];
