<div class="row g-4 mb-4">
    <?php $s = $analytics['stats']; ?>
    <div class="col-md-4"><div class="stat-card"><div class="stat-value"><?= formatMoney($s['monthly']) ?></div><div class="stat-label">Monthly Revenue</div></div></div>
    <div class="col-md-4"><div class="stat-card accent"><div class="stat-value"><?= $s['total'] ?></div><div class="stat-label">Total Orders</div></div></div>
    <div class="col-md-4"><div class="stat-card"><div class="stat-value"><?= $analytics['studentCount'] ?></div><div class="stat-label">Registered Students</div></div></div>
</div>
<div class="d-flex gap-2 mb-4">
    <a href="<?= url('/admin/reports/pdf') ?>" class="btn btn-primary-ecafe"><i class="fas fa-file-pdf me-2"></i>Export PDF</a>
    <a href="<?= url('/admin/reports/excel') ?>" class="btn btn-accent"><i class="fas fa-file-excel me-2"></i>Export Excel</a>
</div>
<h5>Popular Foods</h5>
<ul class="list-group">
<?php foreach ($analytics['popularFoods'] as $food): ?>
<li class="list-group-item d-flex justify-content-between"><?= e($food['name']) ?> <span class="badge bg-primary"><?= $food['order_count'] ?? 0 ?></span></li>
<?php endforeach; ?>
</ul>
