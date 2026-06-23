<div class="glass-card p-4 mb-4">
    <h4>Welcome, <?= e($student['full_name']) ?>! <span class="text-muted fs-6">(<?= e($student['student_id']) ?>)</span></h4>
    <p class="text-muted mb-0">You have <strong class="text-primary"><?= (int)$student['loyalty_points'] ?></strong> loyalty points.</p>
</div>

<?php foreach ($announcements as $ann): ?>
<div class="alert alert-info"><i class="fas fa-bullhorn me-2"></i><strong><?= e($ann['title']) ?></strong> — <?= e($ann['content']) ?></div>
<?php endforeach; ?>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-value"><?= $cartCount ?></div>
            <div class="stat-label">Items in Cart</div>
            <a href="<?= url('/student/cart') ?>" class="btn btn-sm btn-primary-ecafe mt-2">View Cart</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center accent">
            <div class="stat-value"><?= count($recentOrders) ?></div>
            <div class="stat-label">Recent Orders</div>
            <a href="<?= url('/student/orders') ?>" class="btn btn-sm btn-accent mt-2">View Orders</a>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card text-center">
            <div class="stat-value"><i class="fas fa-star text-warning"></i></div>
            <div class="stat-label">Loyalty Points</div>
            <span class="badge bg-primary fs-6"><?= (int)$student['loyalty_points'] ?></span>
        </div>
    </div>
</div>

<h5 class="mb-3">Recent Orders</h5>
<?php if (empty($recentOrders)): ?>
<p class="text-muted">No orders yet. <a href="<?= url('/student/menu') ?>">Browse the menu</a></p>
<?php else: ?>
<div class="table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Order #</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($recentOrders as $order): ?>
        <tr>
            <td><?= e($order['order_number']) ?></td>
            <td><?= formatMoney((float)$order['total_amount']) ?></td>
            <td><span class="badge status-<?= e($order['status']) ?>"><?= ucfirst(e($order['status'])) ?></span></td>
            <td><?= date('M j, g:i A', strtotime($order['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
