<?php

declare(strict_types=1);

namespace App\Helpers;

class Sanitizer
{
    public static function string(?string $value): string
    {
        return trim($value ?? '');
    }

    public static function email(?string $value): string
    {
        return filter_var(trim($value ?? ''), FILTER_SANITIZE_EMAIL) ?: '';
    }

    public static function int(mixed $value): int
    {
        return (int) filter_var($value, FILTER_VALIDATE_INT) ?: 0;
    }

    public static function float(mixed $value): float
    {
        return (float) filter_var($value, FILTER_VALIDATE_FLOAT) ?: 0.0;
    }

    public static function escape(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function phone(?string $value): string
    {
        return preg_replace('/[^0-9+]/', '', $value ?? '') ?? '';
    }
}
