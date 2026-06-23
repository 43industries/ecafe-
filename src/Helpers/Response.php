<?php

declare(strict_types=1);

namespace App\Helpers;

class Response
{
    public static function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public static function redirect(string $url, int $status = 302): void
    {
        header('Location: ' . $url, true, $status);
        exit;
    }

    public static function view(string $template, array $data = [], ?string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);
        $config = require ECAFE_ROOT . '/config/app.php';
        $csrfToken = Session::csrfToken();
        $currentUser = Session::get('user');
        $flashSuccess = Session::flash('success');
        $flashError = Session::flash('error');

        ob_start();
        require ECAFE_ROOT . '/views/' . $template . '.php';
        $content = ob_get_clean();

        if ($layout) {
            require ECAFE_ROOT . '/views/layouts/' . $layout . '.php';
        } else {
            echo $content;
        }
        exit;
    }

    public static function dashboard(string $template, array $data = [], string $role = 'student'): void
    {
        $data['dashboardRole'] = $role;
        self::view($template, $data, 'dashboard');
    }
}
