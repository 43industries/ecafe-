<div class="d-flex justify-content-between mb-4">
    <h5>Categories</h5>
    <button class="btn btn-primary-ecafe btn-sm" data-bs-toggle="modal" data-bs-target="#addCat">Add Category</button>
</div>
<div class="row g-3">
<?php foreach ($categories as $cat): ?>
<div class="col-md-4"><div class="card-ecafe p-3"><h6><?= e($cat['name']) ?></h6><small class="text-muted"><?= e($cat['slug']) ?></small></div></div>
<?php endforeach; ?>
</div>
<div class="modal fade" id="addCat" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <form method="POST" action="<?= url('/admin/categories') ?>">
            <input type="hidden" name="_csrf_token" value="<?= e($csrfToken) ?>">
            <div class="modal-header"><h5>Add Category</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input name="name" class="form-control mb-2" placeholder="Name" required>
                <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
                <input name="sort_order" type="number" class="form-control" placeholder="Sort Order" value="0">
            </div>
            <div class="modal-footer"><button class="btn btn-primary-ecafe">Create</button></div>
        </form>
    </div></div>
</div>
