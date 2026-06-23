<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="stat-card"><div class="stat-value"><?= count($pendingOrders) ?></div><div class="stat-label">Active Orders</div></div>
    </div>
    <div class="col-md-6">
        <div class="stat-card accent"><div class="stat-value"><?= count($lowStock) ?></div><div class="stat-label">Low Stock Items</div></div>
    </div>
</div>

<h5 class="mb-3">Pending Orders</h5>
<?php if (empty($pendingOrders)): ?>
<p class="text-muted">No pending orders. Great job!</p>
<?php else: ?>
<?php foreach (array_slice($pendingOrders, 0, 5) as $order): ?>
<div class="card-ecafe p-3 mb-2 d-flex justify-content-between align-items-center">
    <div><strong><?= e($order['order_number']) ?></strong> — <?= e($order['student_name']) ?><br><small class="text-muted"><?= formatMoney((float)$order['total_amount']) ?></small></div>
    <span class="badge status-<?= e($order['status']) ?>"><?= ucfirst(e($order['status'])) ?></span>
</div>
<?php endforeach; ?>
<a href="<?= url('/staff/orders') ?>" class="btn btn-sm btn-primary-ecafe">View All Orders</a>
<?php endif; ?>
