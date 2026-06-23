<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Services\AuthService;

class AuthController
{
    public function loginForm(): void
    {
        if (Session::has('user')) {
            $this->redirectByRole(Session::get('user')['role']);
        }
        Response::view('public/login', ['title' => 'Login']);
    }

    public function login(): void
    {
        $data = [
            'identifier' => Sanitizer::string($_POST['identifier'] ?? ''),
            'password' => $_POST['password'] ?? '',
        ];

        $validator = new Validator($data);
        $validator->required('identifier')->required('password');

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            Response::redirect(url('/login'));
        }

        $auth = new AuthService();
        $user = $auth->login($data['identifier'], $data['password']);

        if (!$user) {
            Session::flash('error', 'Invalid credentials. Please try again.');
            Response::redirect(url('/login'));
        }

        Session::regenerate();
        Session::set('user', $user);
        $this->redirectByRole($user['role']);
    }

    public function logout(): void
    {
        (new AuthService())->logout();
        Response::redirect(url('/'));
    }

    private function redirectByRole(string $role): void
    {
        $routes = [
            'student' => '/student/dashboard',
            'staff' => '/staff/dashboard',
            'admin' => '/admin/dashboard',
        ];
        Response::redirect(url($routes[$role] ?? '/'));
    }
}
