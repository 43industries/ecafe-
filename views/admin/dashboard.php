<?php $s = $analytics['stats']; ?>
<div class="row g-4 mb-4">
    <div class="col-md-3"><div class="stat-card"><div class="stat-value"><?= $s['total'] ?></div><div class="stat-label">Total Orders</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value"><?= formatMoney($s['daily']) ?></div><div class="stat-label">Daily Sales</div></div></div>
    <div class="col-md-3"><div class="stat-card accent"><div class="stat-value"><?= formatMoney($s['weekly']) ?></div><div class="stat-label">Weekly Sales</div></div></div>
    <div class="col-md-3"><div class="stat-card"><div class="stat-value"><?= $analytics['studentCount'] ?></div><div class="stat-label">Students</div></div></div>
</div>
<div class="row g-4">
    <div class="col-lg-8"><div class="card-ecafe p-4"><h6 class="mb-3">Sales (Last 7 Days)</h6><canvas id="salesChart" height="120"></canvas></div></div>
    <div class="col-lg-4"><div class="card-ecafe p-4"><h6 class="mb-3">Popular Foods</h6><canvas id="popularChart"></canvas></div></div>
</div>
