<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e($csrfToken) ?>">
    <title><?= e($title ?? 'Dashboard') ?> | <?= e($config['name']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/theme.css') ?>" rel="stylesheet">
    <link href="<?= asset('css/dashboards.css') ?>" rel="stylesheet">
    <script>const ECAFE_BASE = '<?= e($config['url']) ?>';</script>
</head>
<body>
    <div class="dashboard-wrapper">
        <?php require ECAFE_ROOT . '/views/partials/sidebar.php'; ?>
        <div class="sidebar-overlay"></div>
        <div class="dashboard-main">
            <?php require ECAFE_ROOT . '/views/partials/topbar.php'; ?>
            <div class="dashboard-content page-enter">
                <?php if ($flashSuccess): ?><div class="alert alert-success"><?= e($flashSuccess) ?></div><?php endif; ?>
                <?php if ($flashError): ?><div class="alert alert-danger"><?= e($flashError) ?></div><?php endif; ?>
                <?= $content ?>
            </div>
        </div>
    </div>
    <div id="global-spinner"><div class="spinner-border text-light" style="width:3rem;height:3rem;"></div></div>
    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/cart.js') ?>"></script>
    <script src="<?= asset('js/orders-poll.js') ?>"></script>
    <?php if (($dashboardRole ?? '') === 'admin'): ?>
    <script src="<?= asset('js/admin-charts.js') ?>"></script>
    <?php endif; ?>
</body>
</html>
