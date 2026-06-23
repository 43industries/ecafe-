<?php

declare(strict_types=1);

namespace App\Models;

class OrderModel extends BaseModel
{
    public function generateOrderNumber(): string
    {
        $date = date('Ymd');
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM orders WHERE DATE(created_at) = CURDATE()"
        );
        $stmt->execute();
        $seq = (int) $stmt->fetchColumn() + 1;
        return sprintf('EC-%s-%04d', $date, $seq);
    }

    public function create(array $order, array $items): int
    {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO orders (student_id, order_number, status, subtotal, discount_amount, total_amount, loyalty_points_used, pickup_time, notes, coupon_id)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                $order['student_id'], $order['order_number'], $order['status'] ?? 'pending',
                $order['subtotal'], $order['discount_amount'], $order['total_amount'],
                $order['loyalty_points_used'] ?? 0, $order['pickup_time'], $order['notes'] ?? null,
                $order['coupon_id'] ?? null,
            ]);
            $orderId = (int) $this->db->lastInsertId();

            $itemStmt = $this->db->prepare(
                'INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, unit_price, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?)'
            );
            foreach ($items as $item) {
                $itemStmt->execute([
                    $orderId, $item['menu_item_id'], $item['item_name'],
                    $item['quantity'], $item['unit_price'], $item['subtotal'],
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, s.full_name AS student_name, s.student_id AS student_number
             FROM orders o JOIN students s ON s.id = o.student_id WHERE o.id = ?'
        );
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if (!$order) {
            return null;
        }
        $order['items'] = $this->getItems($id);
        $order['payment'] = $this->getPayment($id);
        return $order;
    }

    public function findByOrderNumber(string $number): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE order_number = ?');
        $stmt->execute([$number]);
        return $stmt->fetch() ?: null;
    }

    public function getItems(int $orderId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = ?');
        $stmt->execute([$orderId]);
        return $stmt->fetchAll();
    }

    public function getPayment(int $orderId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE order_id = ? ORDER BY id DESC LIMIT 1');
        $stmt->execute([$orderId]);
        return $stmt->fetch() ?: null;
    }

    public function getByStudent(int $studentId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM orders WHERE student_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?'
        );
        $stmt->bindValue(1, $studentId, \PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByStatus(?string $status = null, int $limit = 50): array
    {
        if ($status) {
            $stmt = $this->db->prepare(
                'SELECT o.*, s.full_name AS student_name FROM orders o
                 JOIN students s ON s.id = o.student_id
                 WHERE o.status = ? ORDER BY o.created_at ASC LIMIT ?'
            );
            $stmt->bindValue(1, $status, \PDO::PARAM_STR);
            $stmt->bindValue(2, $limit, \PDO::PARAM_INT);
        } else {
            $stmt = $this->db->prepare(
                'SELECT o.*, s.full_name AS student_name FROM orders o
                 JOIN students s ON s.id = o.student_id
                 WHERE o.status NOT IN ("completed","cancelled","rejected")
                 ORDER BY o.created_at ASC LIMIT ?'
            );
            $stmt->bindValue(1, $limit, \PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): bool
    {
        $stmt = $this->db->prepare('UPDATE orders SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function setQrCode(int $id, string $qrPath): void
    {
        $stmt = $this->db->prepare('UPDATE orders SET qr_code = ? WHERE id = ?');
        $stmt->execute([$qrPath, $id]);
    }

    public function getStats(): array
    {
        $total = (int) $this->db->query('SELECT COUNT(*) FROM orders')->fetchColumn();
        $daily = (float) $this->db->query(
            "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE DATE(created_at)=CURDATE() AND status NOT IN ('cancelled','rejected')"
        )->fetchColumn();
        $weekly = (float) $this->db->query(
            "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) AND status NOT IN ('cancelled','rejected')"
        )->fetchColumn();
        $monthly = (float) $this->db->query(
            "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) AND status NOT IN ('cancelled','rejected')"
        )->fetchColumn();
        return compact('total', 'daily', 'weekly', 'monthly');
    }

    public function getSalesByDay(int $days = 7): array
    {
        $stmt = $this->db->prepare(
            "SELECT DATE(created_at) AS day, COUNT(*) AS orders, SUM(total_amount) AS revenue
             FROM orders WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
             AND status NOT IN ('cancelled','rejected')
             GROUP BY DATE(created_at) ORDER BY day"
        );
        $stmt->execute([$days]);
        return $stmt->fetchAll();
    }
}
