<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Helpers\Session;
use App\Models\AnnouncementModel;
use App\Models\CategoryModel;
use App\Models\CouponModel;
use App\Models\MenuModel;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Models\StaffModel;
use App\Models\StudentModel;
use App\Services\ReportService;

class AdminController
{
    public function dashboard(): void
    {
        Response::dashboard('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'analytics' => (new ReportService())->getAnalyticsData(),
        ], 'admin');
    }

    public function students(): void
    {
        Response::dashboard('admin/students', [
            'title' => 'Manage Students',
            'students' => (new StudentModel())->all(),
        ], 'admin');
    }

    public function studentCreate(): void
    {
        $data = [
            'student_id' => Sanitizer::string($_POST['student_id'] ?? ''),
            'email' => Sanitizer::email($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? 'Password123!',
            'full_name' => Sanitizer::string($_POST['full_name'] ?? ''),
            'phone' => Sanitizer::phone($_POST['phone'] ?? ''),
            'grade' => Sanitizer::string($_POST['grade'] ?? ''),
        ];
        (new StudentModel())->create($data);
        Session::flash('success', 'Student created.');
        Response::redirect(url('/admin/students'));
    }

    public function studentDelete(string $id): void
    {
        (new StudentModel())->delete((int) $id);
        Session::flash('success', 'Student deactivated.');
        Response::redirect(url('/admin/students'));
    }

    public function staff(): void
    {
        Response::dashboard('admin/staff', [
            'title' => 'Manage Staff',
            'staff' => (new StaffModel())->all(),
        ], 'admin');
    }

    public function staffCreate(): void
    {
        $data = [
            'username' => Sanitizer::string($_POST['username'] ?? ''),
            'email' => Sanitizer::email($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? 'Password123!',
            'full_name' => Sanitizer::string($_POST['full_name'] ?? ''),
            'phone' => Sanitizer::phone($_POST['phone'] ?? ''),
        ];
        (new StaffModel())->create($data);
        Session::flash('success', 'Staff member created.');
        Response::redirect(url('/admin/staff'));
    }

    public function menu(): void
    {
        Response::dashboard('admin/menu', [
            'title' => 'Menu Management',
            'items' => (new MenuModel())->search(null, null, 100, 0),
            'categories' => (new CategoryModel())->all(),
        ], 'admin');
    }

    public function menuCreate(): void
    {
        $name = Sanitizer::string($_POST['name'] ?? '');
        $data = [
            'category_id' => Sanitizer::int($_POST['category_id'] ?? 0),
            'name' => $name,
            'slug' => strtolower(preg_replace('/[^a-z0-9]+/', '-', $name)),
            'description' => Sanitizer::string($_POST['description'] ?? ''),
            'price' => Sanitizer::float($_POST['price'] ?? 0),
            'is_available' => isset($_POST['is_available']) ? 1 : 0,
            'is_special' => isset($_POST['is_special']) ? 1 : 0,
            'stock' => Sanitizer::int($_POST['stock'] ?? 50),
        ];
        (new MenuModel())->create($data);
        Session::flash('success', 'Menu item created.');
        Response::redirect(url('/admin/menu'));
    }

    public function categories(): void
    {
        Response::dashboard('admin/categories', [
            'title' => 'Categories',
            'categories' => (new CategoryModel())->all(),
        ], 'admin');
    }

    public function categoryCreate(): void
    {
        $name = Sanitizer::string($_POST['name'] ?? '');
        (new CategoryModel())->create([
            'name' => $name,
            'slug' => strtolower(preg_replace('/[^a-z0-9]+/', '-', $name)),
            'description' => Sanitizer::string($_POST['description'] ?? ''),
            'sort_order' => Sanitizer::int($_POST['sort_order'] ?? 0),
        ]);
        Session::flash('success', 'Category created.');
        Response::redirect(url('/admin/categories'));
    }

    public function orders(): void
    {
        Response::dashboard('admin/orders', [
            'title' => 'All Orders',
            'orders' => (new OrderModel())->getByStatus(null, 100),
        ], 'admin');
    }

    public function payments(): void
    {
        Response::dashboard('admin/payments', [
            'title' => 'Payments',
            'payments' => (new PaymentModel())->all(),
        ], 'admin');
    }

    public function announcements(): void
    {
        Response::dashboard('admin/announcements', [
            'title' => 'Announcements',
            'announcements' => (new AnnouncementModel())->all(),
        ], 'admin');
    }

    public function announcementCreate(): void
    {
        (new AnnouncementModel())->create([
            'title' => Sanitizer::string($_POST['title'] ?? ''),
            'content' => Sanitizer::string($_POST['content'] ?? ''),
            'target_role' => Sanitizer::string($_POST['target_role'] ?? 'all'),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);
        Session::flash('success', 'Announcement created.');
        Response::redirect(url('/admin/announcements'));
    }

    public function reports(): void
    {
        Response::dashboard('admin/reports', [
            'title' => 'Reports',
            'analytics' => (new ReportService())->getAnalyticsData(),
        ], 'admin');
    }

    public function exportPdf(): void
    {
        $path = (new ReportService())->salesPdf();
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="sales_report.pdf"');
        readfile($path);
        exit;
    }

    public function exportExcel(): void
    {
        $path = (new ReportService())->salesExcel();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="sales_report.xlsx"');
        readfile($path);
        exit;
    }

    public function settings(): void
    {
        Response::dashboard('admin/settings', ['title' => 'Settings'], 'admin');
    }
}
