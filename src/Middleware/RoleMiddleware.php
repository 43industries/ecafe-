<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Response;
use App\Helpers\Session;

class RoleMiddleware
{
    public function __construct(private string $role) {}

    public function handle(): void
    {
        $user = Session::get('user');
        if (!$user || ($user['role'] ?? '') !== $this->role) {
            http_response_code(403);
            Response::view('public/403', ['title' => 'Access Denied'], 'main');
        }
    }
}
