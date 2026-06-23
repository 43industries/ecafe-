<div class="d-flex justify-content-between mb-4">
    <h5>Menu Items</h5>
    <button class="btn btn-primary-ecafe btn-sm" data-bs-toggle="modal" data-bs-target="#addItem">Add Item</button>
</div>
<div class="table-responsive">
    <table class="table table-hover">
        <thead><tr><th>Name</th><th>Category</th><th>Price</th><th>Available</th><th>Special</th></tr></thead>
        <tbody>
        <?php foreach ($items as $item): ?>
        <tr>
            <td><?= e($item['name']) ?></td>
            <td><?= e($item['category_name']) ?></td>
            <td><?= formatMoney((float)$item['price']) ?></td>
            <td><?= $item['is_available'] ? 'Yes' : 'No' ?></td>
            <td><?= $item['is_special'] ? '⭐' : '' ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="modal fade" id="addItem" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="<?= url('/admin/menu') ?>">
            <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
            <div class="modal-header"><h5>Add Menu Item</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input name="name" class="form-control mb-2" placeholder="Name" required>
                <select name="category_id" class="form-select mb-2" required>
                    <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?>
                </select>
                <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
                <input name="price" type="number" step="0.01" class="form-control mb-2" placeholder="Price" required>
                <input name="stock" type="number" class="form-control mb-2" placeholder="Stock" value="50">
                <div class="form-check"><input type="checkbox" name="is_available" class="form-check-input" checked> Available</div>
                <div class="form-check"><input type="checkbox" name="is_special" class="form-check-input"> Daily Special</div>
            </div>
            <div class="modal-footer"><button class="btn btn-primary-ecafe">Create</button></div>
        </form>
    </div></div>
</div>
