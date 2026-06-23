<div class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
        <h1 class="topbar-title"><?= e($title ?? 'Dashboard') ?></h1>
    </div>
    <div class="d-flex align-items-center gap-3">
        <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary" title="Dark mode"><i class="fas fa-moon"></i></button>
        <?php if (($dashboardRole ?? '') === 'student'): ?>
        <a href="<?= url('/student/cart') ?>" class="btn btn-sm btn-outline-primary position-relative">
            <i class="fas fa-shopping-cart"></i>
            <span id="cart-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem"><?= $cartCount ?? 0 ?></span>
        </a>
        <a href="<?= url('/student/notifications') ?>" class="btn btn-sm btn-outline-primary position-relative">
            <i class="fas fa-bell"></i>
            <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.65rem;display:none">0</span>
        </a>
        <?php endif; ?>
        <span class="text-muted small"><i class="fas fa-user-circle me-1"></i><?= e($currentUser['name'] ?? '') ?></span>
    </div>
</div>
