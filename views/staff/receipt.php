<!DOCTYPE html>
<html><head><title>Receipt - <?= e($order['order_number']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>@media print { .no-print { display: none; } }</style>
</head><body class="p-4">
<div class="container" style="max-width:400px">
    <h4 class="text-center">School e-Café</h4>
    <p class="text-center text-muted small">Order Receipt</p>
    <hr>
    <p><strong>Order:</strong> <?= e($order['order_number']) ?></p>
    <p><strong>Student:</strong> <?= e($order['student_name']) ?> (<?= e($order['student_number']) ?>)</p>
    <p><strong>Date:</strong> <?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></p>
    <p><strong>Pickup:</strong> <?= date('g:i A', strtotime($order['pickup_time'])) ?></p>
    <hr>
    <table class="table table-sm">
        <thead><tr><th>Item</th><th>Qty</th><th>Price</th></tr></thead>
        <tbody>
        <?php foreach ($order['items'] as $item): ?>
        <tr><td><?= e($item['item_name']) ?></td><td><?= $item['quantity'] ?></td><td><?= formatMoney((float)$item['subtotal']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <hr>
    <div class="d-flex justify-content-between"><span>Subtotal</span><span><?= formatMoney((float)$order['subtotal']) ?></span></div>
    <?php if ($order['discount_amount'] > 0): ?>
    <div class="d-flex justify-content-between text-success"><span>Discount</span><span>-<?= formatMoney((float)$order['discount_amount']) ?></span></div>
    <?php endif; ?>
    <div class="d-flex justify-content-between fw-bold"><span>Total</span><span><?= formatMoney((float)$order['total_amount']) ?></span></div>
    <?php if ($order['payment']): ?>
    <p class="mt-2 small">Payment: <?= ucfirst(e($order['payment']['method'])) ?> — <?= ucfirst(e($order['payment']['status'])) ?></p>
    <?php endif; ?>
    <button class="btn btn-primary w-100 mt-3 no-print" onclick="window.print()">Print Receipt</button>
</div>
</body></html>
