<nav class="navbar navbar-expand-lg navbar-ecafe sticky-top">
    <div class="container">
        <a class="navbar-brand" href="<?= url('/') ?>"><i class="fas fa-utensils me-2"></i>School <span class="accent">e-Café</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= url('/') ?>">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('/about') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('/menu') ?>">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= url('/contact') ?>">Contact</a></li>
            </ul>
            <div class="d-flex align-items-center gap--2">
                <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary me-2" title="Toggle dark mode"><i class="fas fa-moon"></i></button>
                <?php if ($currentUser): ?>
                    <a href="<?= url('/logout') ?>" class="btn btn-sm btn-outline-danger">Logout</a>
                <?php else: ?>
                    <a href="<?= url('/login') ?>" class="btn btn-primary-ecafe btn-sm">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
