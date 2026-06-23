<form method="GET" class="row g-3 mb-4">
    <div class="col-md-5"><input type="text" name="q" class="form-control" placeholder="Search..." value="<?= e($search ?? '') ?>"></div>
    <div class="col-md-4">
        <select name="category" class="form-select">
            <option value="">All Categories</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= ($categoryId ?? '') == $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3"><button class="btn btn-primary-ecafe w-100">Filter</button></div>
</form>

<div class="row">
<?php foreach ($items as $item): $showCart = true; $showFavorite = true; require ECAFE_ROOT . '/views/partials/food-card.php'; endforeach; ?>
</div>

<?php if (($total ?? 0) > ($perPage ?? 12)): ?>
<nav class="mt-4"><ul class="pagination justify-content-center">
<?php for ($p = 1; $p <= ceil($total / $perPage); $p++): ?>
<li class="page-item <?= $p == $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $p ?>&q=<?= urlencode($search ?? '') ?>&category=<?= $categoryId ?? '' ?>"><?= $p ?></a></li>
<?php endfor; ?>
</ul></nav>
<?php endif; ?>

<script>
async function toggleFavorite(id) {
    const res = await ECafe.fetch(ECAFE_BASE + '/api/favorites/toggle', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'menu_item_id=' + id
    });
    if (res.success) ECafe.toast(res.favorited ? 'Added to favorites' : 'Removed from favorites');
}
</script>
