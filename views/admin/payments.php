<div class="table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Order #</th><th>Method</th><th>Amount</th><th>Status</th><th>Receipt</th><th>Date</th></tr></thead>
        <tbody>
        <?php foreach ($payments as $p): ?>
        <tr>
            <td><?= e($p['order_number']) ?></td>
            <td><?= ucfirst(e($p['method'])) ?></td>
            <td><?= formatMoney((float)$p['amount']) ?></td>
            <td><span class="badge <?= $p['status'] === 'paid' ? 'bg-success' : 'bg-warning' ?>"><?= ucfirst(e($p['status'])) ?></span></td>
            <td><?= e($p['mpesa_receipt'] ?? '-') ?></td>
            <td><?= date('M j, Y', strtotime($p['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
