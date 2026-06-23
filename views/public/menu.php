<div class="container my-5 page-enter">
    <h1 class="fw-bold mb-4">Our <span class="text-primary">Menu</span></h1>

    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-6">
            <input type="text" name="q" class="form-control" placeholder="Search food..." value="<?= e($search ?? '') ?>">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><button class="btn btn-primary-ecafe w-100">Search</button></div>
    </form>

    <div class="mb-4">
        <?php foreach ($categories as $cat): ?>
        <a href="?category=<?= $cat['id'] ?>" class="category-chip <?= ($categoryId ?? '') == $cat['id'] ? 'active' : '' ?>"><?= e($cat['name']) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <?php if (empty($items)): ?>
        <div class="col-12 text-center text-muted py-5"><i class="fas fa-search fa-3x mb-3"></i><p>No items found.</p></div>
        <?php else: ?>
        <?php foreach ($items as $item): $showCart = false; require ECAFE_ROOT . '/views/partials/food-card.php'; endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if (($total ?? 0) > ($perPage ?? 12)): ?>
    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($p = 1; $p <= ceil($total / $perPage); $p++): ?>
            <li class="page-item <?= $p == $page ? 'active' : '' ?>">
                <a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($search ?? '') ?>&category=<?= $categoryId ?? '' ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
        </ul>
    </nav>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="<?= url('/login') ?>" class="btn btn-accent">Login to Order</a>
    </div>
</div>
