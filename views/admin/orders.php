<div class="table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Order #</th><th>Student</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($orders as $o): ?>
        <tr>
            <td><?= e($o['order_number']) ?></td>
            <td><?= e($o['student_name']) ?></td>
            <td><?= formatMoney((float)$o['total_amount']) ?></td>
            <td><span class="badge status-<?= e($o['status']) ?>"><?= ucfirst(e($o['status'])) ?></span></td>
            <td><?= date('M j, Y', strtotime($o['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
