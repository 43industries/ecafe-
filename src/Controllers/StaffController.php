<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Helpers\Response;
use App\Helpers\Sanitizer;
use App\Models\InventoryModel;
use App\Models\OrderModel;
use App\Services\OrderService;
use App\Services\ReportService;

class StaffController
{
    public function dashboard(): void
    {
        $orders = new OrderModel();
        $inventory = new InventoryModel();
        Response::dashboard('staff/dashboard', [
            'title' => 'Staff Dashboard',
            'pendingOrders' => $orders->getByStatus(null, 10),
            'lowStock' => $inventory->getLowStock(),
        ], 'staff');
    }

    public function orders(): void
    {
        $status = $_GET['status'] ?? null;
        Response::dashboard('staff/orders', [
            'title' => 'Orders',
            'orders' => (new OrderModel())->getByStatus($status, 50),
            'currentStatus' => $status,
        ], 'staff');
    }

    public function updateOrder(): void
    {
        $orderId = Sanitizer::int($_POST['order_id'] ?? 0);
        $action = Sanitizer::string($_POST['action'] ?? '');
        $statusMap = [
            'accept' => 'accepted',
            'reject' => 'rejected',
            'preparing' => 'preparing',
            'ready' => 'ready',
            'complete' => 'completed',
        ];

        if (!isset($statusMap[$action])) {
            Response::json(['success' => false, 'message' => 'Invalid action.']);
        }

        (new OrderService())->updateStatus($orderId, $statusMap[$action]);
        Response::json(['success' => true, 'message' => 'Order updated.']);
    }

    public function inventory(): void
    {
        Response::dashboard('staff/inventory', [
            'title' => 'Inventory',
            'items' => (new InventoryModel())->all(),
        ], 'staff');
    }

    public function updateInventory(): void
    {
        $menuItemId = Sanitizer::int($_POST['menu_item_id'] ?? 0);
        $quantity = Sanitizer::int($_POST['quantity'] ?? 0);
        (new InventoryModel())->updateQuantity($menuItemId, $quantity);
        Response::json(['success' => true, 'message' => 'Inventory updated.']);
    }

    public function reports(): void
    {
        Response::dashboard('staff/reports', [
            'title' => 'Reports',
            'analytics' => (new ReportService())->getAnalyticsData(),
        ], 'staff');
    }

    public function receipt(string $id): void
    {
        $order = (new OrderModel())->findById((int) $id);
        if (!$order) {
            http_response_code(404);
            exit('Order not found');
        }
        Response::view('staff/receipt', ['order' => $order], null);
    }
}
