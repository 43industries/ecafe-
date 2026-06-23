<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\Session;
use App\Models\FavoriteModel;
use App\Models\MenuModel;
use App\Models\NotificationModel;
use App\Models\OrderModel;
use App\Services\CartService;
use App\Services\MpesaService;
use App\Services\ReportService;

class ApiController
{
    public function cartAdd(): void
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'student') {
            Response::json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $result = (new CartService())->add(
            (int) $user['id'],
            Sanitizer::int($_POST['menu_item_id'] ?? 0),
            max(1, Sanitizer::int($_POST['quantity'] ?? 1))
        );
        Response::json($result);
    }

    public function cartRemove(): void
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'student') {
            Response::json(['success' => false], 401);
        }
        (new CartService())->remove((int) $user['id'], Sanitizer::int($_POST['menu_item_id'] ?? 0));
        Response::json(['success' => true]);
    }

    public function cartUpdate(): void
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'student') {
            Response::json(['success' => false], 401);
        }
        $result = (new CartService())->update(
            (int) $user['id'],
            Sanitizer::int($_POST['menu_item_id'] ?? 0),
            Sanitizer::int($_POST['quantity'] ?? 1)
        );
        Response::json($result);
    }

    public function menuSearch(): void
    {
        $menu = new MenuModel();
        $q = $_GET['q'] ?? null;
        $category = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        Response::json([
            'items' => $menu->search($q, $category, $perPage, ($page - 1) * $perPage),
            'total' => $menu->countSearch($q, $category),
            'page' => $page,
        ]);
    }

    public function orderStatus(string $id): void
    {
        $order = (new OrderModel())->findById((int) $id);
        if (!$order) {
            Response::json(['success' => false], 404);
        }
        Response::json([
            'success' => true,
            'status' => $order['status'],
            'order_number' => $order['order_number'],
            'qr_code' => $order['qr_code'],
        ]);
    }

    public function notifications(): void
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'student') {
            Response::json(['success' => false], 401);
        }
        $model = new NotificationModel();
        Response::json([
            'unread' => $model->unreadCount((int) $user['id']),
            'items' => $model->getByStudent((int) $user['id'], 10),
        ]);
    }

    public function notificationsRead(): void
    {
        $user = Session::get('user');
        if (!$user) {
            Response::json(['success' => false], 401);
        }
        $model = new NotificationModel();
        $id = Sanitizer::int($_POST['id'] ?? 0);
        if ($id) {
            $model->markRead($id, (int) $user['id']);
        } else {
            $model->markAllRead((int) $user['id']);
        }
        Response::json(['success' => true]);
    }

    public function favoritesToggle(): void
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'student') {
            Response::json(['success' => false], 401);
        }
        $added = (new FavoriteModel())->toggle(
            (int) $user['id'],
            Sanitizer::int($_POST['menu_item_id'] ?? 0)
        );
        Response::json(['success' => true, 'favorited' => $added]);
    }

    public function recommendations(): void
    {
        $user = Session::get('user');
        $menu = new MenuModel();
        if ($user && $user['role'] === 'student') {
            $db = \App\Helpers\Database::getConnection();
            $stmt = $db->prepare(
                'SELECT m.* FROM order_items oi
                 JOIN orders o ON o.id = oi.order_id
                 JOIN menu_items m ON m.id = oi.menu_item_id
                 WHERE o.student_id = ? GROUP BY m.id ORDER BY COUNT(*) DESC LIMIT 5'
            );
            $stmt->execute([(int) $user['id']]);
            $items = $stmt->fetchAll();
            if (!empty($items)) {
                Response::json(['items' => $items]);
            }
        }
        Response::json(['items' => $menu->getPopular(5)]);
    }

    public function mpesaStkPush(): void
    {
        $user = Session::get('user');
        if (!$user) {
            Response::json(['success' => false], 401);
        }
        $paymentId = Sanitizer::int($_POST['payment_id'] ?? 0);
        $phone = Sanitizer::phone($_POST['phone'] ?? '');
        $amount = Sanitizer::float($_POST['amount'] ?? 0);
        $orderNumber = Sanitizer::string($_POST['order_number'] ?? '');
        $result = (new MpesaService())->initiateStkPush($paymentId, $phone, $amount, $orderNumber);
        Response::json($result);
    }

    public function mpesaCallback(): void
    {
        $payload = json_decode(file_get_contents('php://input'), true) ?? [];
        (new MpesaService())->handleCallback($payload);
        Response::json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }

    public function adminCharts(): void
    {
        $user = Session::get('user');
        if (!$user || $user['role'] !== 'admin') {
            Response::json(['success' => false], 403);
        }
        Response::json((new ReportService())->getAnalyticsData());
    }
}
