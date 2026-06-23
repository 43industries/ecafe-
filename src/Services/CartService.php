<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CartModel;
use App\Models\MenuModel;

class CartService
{
    private CartModel $cart;
    private MenuModel $menu;

    public function __construct()
    {
        $this->cart = new CartModel();
        $this->menu = new MenuModel();
    }

    public function getCart(int $studentId): array
    {
        return $this->cart->getItems($studentId);
    }

    public function add(int $studentId, int $menuItemId, int $quantity = 1): array
    {
        $item = $this->menu->findById($menuItemId);
        if (!$item || !$item['is_available']) {
            return ['success' => false, 'message' => 'Item is not available.'];
        }
        if (($item['stock'] ?? 0) < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock.'];
        }
        $this->cart->addOrUpdate($studentId, $menuItemId, $quantity);
        return ['success' => true, 'count' => $this->cart->countItems($studentId)];
    }

    public function update(int $studentId, int $menuItemId, int $quantity): array
    {
        $this->cart->setQuantity($studentId, $menuItemId, $quantity);
        return ['success' => true, 'total' => $this->cart->getTotal($studentId)];
    }

    public function remove(int $studentId, int $menuItemId): void
    {
        $this->cart->remove($studentId, $menuItemId);
    }

    public function clear(int $studentId): void
    {
        $this->cart->clear($studentId);
    }

    public function getTotal(int $studentId): float
    {
        return $this->cart->getTotal($studentId);
    }

    public function countItems(int $studentId): int
    {
        return $this->cart->countItems($studentId);
    }
}
