<div class="row g-4 mb-4">
    <?php $s = $analytics['stats']; ?>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value"><?= $s['total'] ?></div><div class="stat-label">Total Orders</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value"><?= formatMoney($s['daily']) ?></div><div class="stat-label">Today's Sales</div></div></div>
    <div class="col-md-3"><div class="stat-card accent"><div class="stat-value"><?= formatMoney($s['weekly']) ?></div><div class="stat-label">Weekly Sales</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value"><?= formatMoney($s['monthly']) ?></div><div class="stat-label">Monthly Sales</div></div></div>
</div>
<h5>Popular Foods</h5>
<ul class="list-group">
<?php foreach ($analytics['popularFoods'] as $food): ?>
<li class="list-group-item d-flex justify-content-between"><?= e($food['name']) ?> <span class="badge bg-primary"><?= $food['order_count'] ?? 0 ?> orders</span></li>
<?php endforeach; ?>
</ul>
