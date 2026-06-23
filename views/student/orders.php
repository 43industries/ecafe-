<h5 class="mb-4">My Orders</h5>
<?php if (empty($orders)): ?>
<p class="text-muted">No orders yet.</p>
<?php else: ?>
<?php foreach ($orders as $order): ?>
<div class="card-ecafe p-4 mb-3" data-poll-order="<?= $order['id'] ?>">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h6 class="mb-1"><?= e($order['order_number']) ?></h6>
            <small class="text-muted"><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></small>
        </div>
        <span class="badge order-status-badge status-<?= e($order['status']) ?>"><?= ucfirst(e($order['status'])) ?></span>
    </div>
    <div class="mt-2 d-flex justify-content-between">
        <span>Pickup: <?= date('g:i A', strtotime($order['pickup_time'])) ?></span>
        <strong><?= formatMoney((float)$order['total_amount']) ?></strong>
    </div>
    <?php if ($order['qr_code']): ?>
    <div class="mt-3 text-center">
        <p class="small text-muted">Show this QR at pickup:</p>
        <img class="qr-code-img" src="<?= url('/' . e($order['qr_code'])) ?>" alt="QR Code" width="120">
    </div>
    <?php endif; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>
