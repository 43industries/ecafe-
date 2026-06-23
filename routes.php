<?php

declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\ApiController;
use App\Controllers\AuthController;
use App\Controllers\PublicController;
use App\Controllers\StaffController;
use App\Controllers\StudentController;
use App\Helpers\Router;

$router = new Router();

// Public routes
$router->get('/', [PublicController::class, 'home']);
$router->get('/about', [PublicController::class, 'about']);
$router->get('/menu', [PublicController::class, 'menu']);
$router->get('/contact', [PublicController::class, 'contact']);
$router->post('/contact', [PublicController::class, 'contactSubmit'], ['CsrfMiddleware']);
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login'], ['CsrfMiddleware']);
$router->get('/logout', [AuthController::class, 'logout']);

// Student routes
$studentMw = ['AuthMiddleware', 'RoleMiddleware:student'];
$router->get('/student/dashboard', [StudentController::class, 'dashboard'], $studentMw);
$router->get('/student/menu', [StudentController::class, 'menu'], $studentMw);
$router->get('/student/cart', [StudentController::class, 'cart'], $studentMw);
$router->get('/student/checkout', [StudentController::class, 'checkout'], $studentMw);
$router->post('/student/checkout', [StudentController::class, 'checkoutSubmit'], array_merge($studentMw, ['CsrfMiddleware']));
$router->get('/student/orders', [StudentController::class, 'orders'], $studentMw);
$router->get('/student/notifications', [StudentController::class, 'notifications'], $studentMw);
$router->get('/student/profile', [StudentController::class, 'profile'], $studentMw);
$router->post('/student/profile', [StudentController::class, 'profileUpdate'], array_merge($studentMw, ['CsrfMiddleware']));

// Staff routes
$staffMw = ['AuthMiddleware', 'RoleMiddleware:staff'];
$router->get('/staff/dashboard', [StaffController::class, 'dashboard'], $staffMw);
$router->get('/staff/orders', [StaffController::class, 'orders'], $staffMw);
$router->post('/staff/orders/update', [StaffController::class, 'updateOrder'], array_merge($staffMw, ['CsrfMiddleware']));
$router->get('/staff/inventory', [StaffController::class, 'inventory'], $staffMw);
$router->post('/staff/inventory/update', [StaffController::class, 'updateInventory'], array_merge($staffMw, ['CsrfMiddleware']));
$router->get('/staff/reports', [StaffController::class, 'reports'], $staffMw);
$router->get('/staff/orders/{id}/receipt', [StaffController::class, 'receipt'], $staffMw);

// Admin routes
$adminMw = ['AuthMiddleware', 'RoleMiddleware:admin'];
$router->get('/admin/dashboard', [AdminController::class, 'dashboard'], $adminMw);
$router->get('/admin/students', [AdminController::class, 'students'], $adminMw);
$router->post('/admin/students', [AdminController::class, 'studentCreate'], array_merge($adminMw, ['CsrfMiddleware']));
$router->post('/admin/students/{id}/delete', [AdminController::class, 'studentDelete'], array_merge($adminMw, ['CsrfMiddleware']));
$router->get('/admin/staff', [AdminController::class, 'staff'], $adminMw);
$router->post('/admin/staff', [AdminController::class, 'staffCreate'], array_merge($adminMw, ['CsrfMiddleware']));
$router->get('/admin/menu', [AdminController::class, 'menu'], $adminMw);
$router->post('/admin/menu', [AdminController::class, 'menuCreate'], array_merge($adminMw, ['CsrfMiddleware']));
$router->get('/admin/categories', [AdminController::class, 'categories'], $adminMw);
$router->post('/admin/categories', [AdminController::class, 'categoryCreate'], array_merge($adminMw, ['CsrfMiddleware']));
$router->get('/admin/orders', [AdminController::class, 'orders'], $adminMw);
$router->get('/admin/payments', [AdminController::class, 'payments'], $adminMw);
$router->get('/admin/announcements', [AdminController::class, 'announcements'], $adminMw);
$router->post('/admin/announcements', [AdminController::class, 'announcementCreate'], array_merge($adminMw, ['CsrfMiddleware']));
$router->get('/admin/reports', [AdminController::class, 'reports'], $adminMw);
$router->get('/admin/reports/pdf', [AdminController::class, 'exportPdf'], $adminMw);
$router->get('/admin/reports/excel', [AdminController::class, 'exportExcel'], $adminMw);
$router->get('/admin/settings', [AdminController::class, 'settings'], $adminMw);

// API routes
$router->post('/api/cart/add', [ApiController::class, 'cartAdd'], ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/api/cart/remove', [ApiController::class, 'cartRemove'], ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/api/cart/update', [ApiController::class, 'cartUpdate'], ['AuthMiddleware', 'CsrfMiddleware']);
$router->get('/api/menu/search', [ApiController::class, 'menuSearch']);
$router->get('/api/orders/status/{id}', [ApiController::class, 'orderStatus'], ['AuthMiddleware']);
$router->get('/api/notifications', [ApiController::class, 'notifications'], ['AuthMiddleware']);
$router->post('/api/notifications/read', [ApiController::class, 'notificationsRead'], ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/api/favorites/toggle', [ApiController::class, 'favoritesToggle'], ['AuthMiddleware', 'CsrfMiddleware']);
$router->get('/api/recommendations', [ApiController::class, 'recommendations']);
$router->post('/api/mpesa/stk-push', [ApiController::class, 'mpesaStkPush'], ['AuthMiddleware', 'CsrfMiddleware']);
$router->post('/api/mpesa/callback', [ApiController::class, 'mpesaCallback']);
$router->get('/api/admin/charts', [ApiController::class, 'adminCharts'], ['AuthMiddleware', 'RoleMiddleware:admin']);

return $router;
