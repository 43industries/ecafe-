<?php
/** @var array $item */
$available = $item['is_available'] && ($item['stock'] ?? 1) > 0;
?>
<div class="col-md-4 col-lg-3 mb-4">
    <div class="card-ecafe food-card h-100">
        <div class="position-relative overflow-hidden">
            <img src="<?= $item['image'] ? asset('img/' . e($item['image'])) : 'https://placehold.co/400x180/2563eb/ffffff?text=' . urlencode($item['name']) ?>" alt="<?= e($item['name']) ?>">
            <?php if ($item['is_special'] ?? false): ?><span class="badge badge-special position-absolute top-0 end-0 m-2">Special</span><?php endif; ?>
            <?php if (isset($showFavorite) && $showFavorite): ?>
            <button class="btn-favorite position-absolute top-0 start-0 m-2 <?= in_array($item['id'], $favoriteIds ?? []) ? 'active' : '' ?>"
                data-favorite="<?= $item['id'] ?>" onclick="toggleFavorite(<?= $item['id'] ?>)">
                <i class="fas fa-heart"></i>
            </button>
            <?php endif; ?>
        </div>
        <div class="card-body p-3">
            <span class="badge <?= $available ? 'badge-available' : 'badge-unavailable' ?> mb-2"><?= $available ? 'Available' : 'Unavailable' ?></span>
            <h6 class="mb-1"><?= e($item['name']) ?></h6>
            <p class="text-muted small mb-2"><?= e($item['category_name'] ?? '') ?></p>
            <div class="d-flex justify-content-between align-items-center">
                <strong class="text-primary"><?= formatMoney((float)$item['price']) ?></strong>
                <?php if ($showCart ?? false): ?>
                <button class="btn btn-accent btn-sm" data-add-cart="<?= $item['id'] ?>" <?= !$available ? 'disabled' : '' ?>>
                    <i class="fas fa-plus"></i> Add
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
