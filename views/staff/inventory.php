<div class="table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Item</th><th>Quantity</th><th>Low Stock At</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
        <tr class="<?= $item['quantity'] <= $item['low_stock_threshold'] ? 'table-warning' : '' ?>">
            <td><?= e($item['item_name']) ?></td>
            <td><input type="number" class="form-control form-control-sm" style="width:80px" id="qty-<?= $item['menu_item_id'] ?>" value="<?= $item['quantity'] ?>"></td>
            <td><?= $item['low_stock_threshold'] ?></td>
            <td><button class="btn btn-sm btn-primary-ecafe" onclick="updateInv(<?= $item['menu_item_id'] ?>)">Update</button></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<script>
async function updateInv(id) {
    const qty = document.getElementById('qty-' + id).value;
    const res = await ECafe.fetch('<?= url('/staff/inventory/update') ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `_csrf_token=${ECafe.csrfToken}&menu_item_id=${id}&quantity=${qty}`
    });
    if (res.success) ECafe.toast('Inventory updated');
}
</script>
