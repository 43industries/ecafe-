<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/bootstrap.php';
require_once ECAFE_ROOT . '/src/Helpers/functions.php';

use App\Helpers\Session;

Session::start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
if ($basePath !== '' && str_starts_with($uri, $basePath)) {
    $uri = substr($uri, strlen($basePath)) ?: '/';
}

$router = require ECAFE_ROOT . '/routes.php';
$router->dispatch($_SERVER['REQUEST_METHOD'], $uri);
