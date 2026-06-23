<div class="mb-3 d-flex gap-2 flex-wrap">
    <a href="<?= url('/staff/orders') ?>" class="btn btn-sm <?= !$currentStatus ? 'btn-primary' : 'btn-outline-primary' ?>">All Active</a>
    <?php foreach (['pending','accepted','preparing','ready'] as $s): ?>
    <a href="?status=<?= $s ?>" class="btn btn-sm <?= $currentStatus === $s ? 'btn-primary' : 'btn-outline-primary' ?>"><?= ucfirst($s) ?></a>
    <?php endforeach; ?>
</div>

<?php foreach ($orders as $order): ?>
<div class="card-ecafe p-4 mb-3" id="order-<?= $order['id'] ?>">
    <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
        <div>
            <h6><?= e($order['order_number']) ?> <span class="badge status-<?= e($order['status']) ?>"><?= ucfirst(e($order['status'])) ?></span></h6>
            <small class="text-muted"><?= e($order['student_name']) ?> · Pickup: <?= date('g:i A', strtotime($order['pickup_time'])) ?></small>
        </div>
        <strong><?= formatMoney((float)$order['total_amount']) ?></strong>
    </div>
    <div class="d-flex gap-2 flex-wrap no-print">
        <?php if ($order['status'] === 'pending'): ?>
        <button class="btn btn-sm btn-success" onclick="updateOrder(<?= $order['id'] ?>, 'accept')">Accept</button>
        <button class="btn btn-sm btn-danger" onclick="updateOrder(<?= $order['id'] ?>, 'reject')">Reject</button>
        <?php elseif ($order['status'] === 'accepted'): ?>
        <button class="btn btn-sm btn-primary" onclick="updateOrder(<?= $order['id'] ?>, 'preparing')">Start Preparing</button>
        <?php elseif ($order['status'] === 'preparing'): ?>
        <button class="btn btn-sm btn-success" onclick="updateOrder(<?= $order['id'] ?>, 'ready')">Mark Ready</button>
        <?php elseif ($order['status'] === 'ready'): ?>
        <button class="btn btn-sm btn-secondary" onclick="updateOrder(<?= $order['id'] ?>, 'complete')">Complete</button>
        <?php endif; ?>
        <a href="<?= url('/staff/orders/' . $order['id'] . '/receipt') ?>" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="fas fa-print"></i> Receipt</a>
    </div>
</div>
<?php endforeach; ?>

<script>
async function updateOrder(id, action) {
    const res = await ECafe.fetch('<?= url('/staff/orders/update') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `_csrf_token=${ECafe.csrfToken}&order_id=${id}&action=${action}`
    });
    if (res.success) { ECafe.toast(res.message); setTimeout(() => location.reload(), 800); }
}
setInterval(() => location.reload(), 15000);
</script>
