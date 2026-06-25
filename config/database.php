<?php

declare(strict_types=1);

$config = [
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_NAME', 'ecafe_db'),
    'username' => env('DB_USER', 'root'),
    'password' => env('DB_PASS', ''),
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];

$url = env('MYSQL_URL') ?: env('DATABASE_URL');
if ($url) {
    $parts = parse_url($url);
    if ($parts !== false) {
        if (!empty($parts['host'])) {
            $config['host'] = $parts['host'];
        }
        if (!empty($parts['port'])) {
            $config['port'] = (string) $parts['port'];
        }
        if (!empty($parts['user'])) {
            $config['username'] = urldecode($parts['user']);
        }
        if (isset($parts['pass'])) {
            $config['password'] = urldecode($parts['pass']);
        }
        if (!empty($parts['path'])) {
            $config['database'] = ltrim($parts['path'], '/');
        }
    }
}

return $config;
