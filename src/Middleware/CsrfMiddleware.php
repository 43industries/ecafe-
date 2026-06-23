<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Response;
use App\Helpers\Session;

class CsrfMiddleware
{
    public function handle(): void
    {
        $token = $_POST['_csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals(Session::csrfToken(), $token)) {
            if ($this->isAjax()) {
                Response::json(['success' => false, 'message' => 'Invalid CSRF token.'], 403);
            }
            Session::flash('error', 'Security token expired. Please try again.');
            Response::redirect($_SERVER['HTTP_REFERER'] ?? url('/'));
        }
    }

    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}
