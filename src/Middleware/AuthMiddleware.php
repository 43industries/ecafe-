<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\Response;
use App\Helpers\Session;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Session::has('user')) {
            Session::flash('error', 'Please log in to continue.');
            Response::redirect(url('/login'));
        }
    }
}
