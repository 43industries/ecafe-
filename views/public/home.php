<?php foreach ($announcements as $ann): ?>
<div class="promo-banner"><i class="fas fa-bullhorn me-2"></i><?= e($ann['title']) ?> — <?= e($ann['content']) ?></div>
<?php endforeach; ?>

<section class="hero-section">
    <div class="container hero-content">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <h1 class="display-4 fw-bold mb-3">Fresh Meals, Fast Pickup</h1>
                <p class="lead mb-4 opacity-90">Order from your school café online. Skip the line, pick up when ready.</p>
                <a href="<?= url('/menu') ?>" class="btn btn-light btn-lg me-2"><i class="fas fa-book-open me-2"></i>Browse Menu</a>
                <a href="<?= url('/login') ?>" class="btn btn-accent btn-lg">Order Now</a>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <i class="fas fa-hamburger" style="font-size:8rem;opacity:0.3"></i>
            </div>
        </div>
    </div>
</section>

<div class="container my-5 page-enter">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Daily <span class="text-primary">Specials</span></h2>
        <p class="text-muted">Today's featured meals at special prices</p>
    </div>
    <div class="row">
        <?php foreach ($specials as $item): $showCart = false; require ECAFE_ROOT . '/views/partials/food-card.php'; endforeach; ?>
    </div>

    <?php if (!empty($popular)): ?>
    <div class="text-center my-5">
        <h2 class="fw-bold">Popular <span class="text-primary">Choices</span></h2>
    </div>
    <div class="row">
        <?php foreach ($popular as $item): $showCart = false; require ECAFE_ROOT . '/views/partials/food-card.php'; endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="row mt-5 g-4">
        <div class="col-md-4">
            <div class="glass-card p-4 text-center h-100">
                <i class="fas fa-mobile-alt fa-2x text-primary mb-3"></i>
                <h5>Order Online</h5>
                <p class="text-muted small">Browse menu and order from your phone or laptop.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center h-100">
                <i class="fas fa-clock fa-2x text-primary mb-3"></i>
                <h5>Schedule Pickup</h5>
                <p class="text-muted small">Choose your preferred pickup time slot.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="glass-card p-4 text-center h-100">
                <i class="fas fa-qrcode fa-2x text-primary mb-3"></i>
                <h5>QR Pickup</h5>
                <p class="text-muted small">Show your QR code for quick order collection.</p>
            </div>
        </div>
    </div>
</div>
