<?php

declare(strict_types=1);

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $config = require ECAFE_ROOT . '/config/app.php';
        $base = $config['url'];
        $path = $path === '' ? '' : '/' . ltrim($path, '/');
        return $base . $path;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('/assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('formatMoney')) {
    function formatMoney(float $amount): string
    {
        $config = require ECAFE_ROOT . '/config/app.php';
        return $config['currency'] . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('roleMiddleware')) {
    function roleMiddleware(string $role): string
    {
        return "RoleMiddleware:{$role}";
    }
}
