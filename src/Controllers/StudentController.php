<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\Session;
use App\Helpers\Validator;
use App\Models\FavoriteModel;
use App\Models\MenuModel;
use App\Models\NotificationModel;
use App\Models\OrderModel;
use App\Models\StudentModel;
use App\Services\CartService;
use App\Services\CouponService;
use App\Services\MpesaService;
use App\Services\OrderService;

class StudentController
{
    private function studentId(): int
    {
        return (int) Session::get('user')['id'];
    }

    public function dashboard(): void
    {
        $student = (new StudentModel())->findById($this->studentId());
        $orders = (new OrderModel())->getByStudent($this->studentId(), 5);
        $announcements = (new \App\Models\AnnouncementModel())->getActive('student');
        Response::dashboard('student/dashboard', [
            'title' => 'Dashboard',
            'student' => $student,
            'recentOrders' => $orders,
            'announcements' => $announcements,
            'cartCount' => (new CartService())->countItems($this->studentId()),
        ], 'student');
    }

    public function menu(): void
    {
        $menu = new MenuModel();
        $favorites = new FavoriteModel();
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $search = $_GET['q'] ?? null;
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        Response::dashboard('student/menu', [
            'title' => 'Menu',
            'categories' => $menu->getCategories(),
            'items' => $menu->search($search, $categoryId, $perPage, $offset),
            'favoriteIds' => $favorites->getFavoriteIds($this->studentId()),
            'total' => $menu->countSearch($search, $categoryId),
            'page' => $page,
            'perPage' => $perPage,
            'search' => $search,
            'categoryId' => $categoryId,
        ], 'student');
    }

    public function cart(): void
    {
        $cart = new CartService();
        Response::dashboard('student/cart', [
            'title' => 'My Cart',
            'items' => $cart->getCart($this->studentId()),
            'total' => $cart->getTotal($this->studentId()),
        ], 'student');
    }

    public function checkout(): void
    {
        $cart = new CartService();
        $student = (new StudentModel())->findById($this->studentId());
        if (empty($cart->getCart($this->studentId()))) {
            Session::flash('error', 'Your cart is empty.');
            Response::redirect(url('/student/cart'));
        }
        Response::dashboard('student/checkout', [
            'title' => 'Checkout',
            'items' => $cart->getCart($this->studentId()),
            'total' => $cart->getTotal($this->studentId()),
            'student' => $student,
        ], 'student');
    }

    public function checkoutSubmit(): void
    {
        $data = [
            'pickup_time' => Sanitizer::string($_POST['pickup_time'] ?? ''),
            'payment_method' => Sanitizer::string($_POST['payment_method'] ?? 'cash'),
            'notes' => Sanitizer::string($_POST['notes'] ?? ''),
            'coupon_code' => Sanitizer::string($_POST['coupon_code'] ?? ''),
            'loyalty_points' => Sanitizer::int($_POST['loyalty_points'] ?? 0),
            'mpesa_phone' => Sanitizer::phone($_POST['mpesa_phone'] ?? ''),
        ];

        $validator = new Validator($data);
        $validator->required('pickup_time')->in('payment_method', ['cash', 'mobile_money', 'card']);

        if ($validator->fails()) {
            Session::flash('error', $validator->firstError());
            Response::redirect(url('/student/checkout'));
        }

        $result = (new OrderService())->checkout($this->studentId(), $data);

        if (!$result['success']) {
            Session::flash('error', $result['message']);
            Response::redirect(url('/student/checkout'));
        }

        if ($data['payment_method'] === 'mobile_money' && !empty($data['mpesa_phone'])) {
            $mpesa = new MpesaService();
            $mpesaResult = $mpesa->initiateStkPush(
                (int) $result['payment_id'],
                $data['mpesa_phone'],
                (float) $result['total'],
                $result['order_number']
            );
            Session::flash($mpesaResult['success'] ? 'success' : 'error', $mpesaResult['message']);
        } else {
            Session::flash('success', 'Order placed successfully! Order #' . $result['order_number']);
        }

        Response::redirect(url('/student/orders'));
    }

    public function orders(): void
    {
        $orders = new OrderModel();
        Response::dashboard('student/orders', [
            'title' => 'My Orders',
            'orders' => $orders->getByStudent($this->studentId()),
        ], 'student');
    }

    public function notifications(): void
    {
        $notifications = new NotificationModel();
        Response::dashboard('student/notifications', [
            'title' => 'Notifications',
            'notifications' => $notifications->getByStudent($this->studentId()),
        ], 'student');
    }

    public function profile(): void
    {
        $student = (new StudentModel())->findById($this->studentId());
        $favorites = (new FavoriteModel())->getByStudent($this->studentId());
        Response::dashboard('student/profile', [
            'title' => 'Profile',
            'student' => $student,
            'favorites' => $favorites,
        ], 'student');
    }

    public function profileUpdate(): void
    {
        $data = [
            'full_name' => Sanitizer::string($_POST['full_name'] ?? ''),
            'email' => Sanitizer::email($_POST['email'] ?? ''),
            'phone' => Sanitizer::phone($_POST['phone'] ?? ''),
            'grade' => Sanitizer::string($_POST['grade'] ?? ''),
            'password' => $_POST['password'] ?? '',
        ];

        $update = $data;
        if (!$data['password']) {
            unset($update['password']);
        }
        (new StudentModel())->update($this->studentId(), $update);
        Session::flash('success', 'Profile updated successfully.');
        Response::redirect(url('/student/profile'));
    }
}
