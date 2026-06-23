<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e($csrfToken) ?>">
    <title><?= e($title ?? 'Home') ?> | <?= e($config['name']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="<?= asset('css/theme.css') ?>" rel="stylesheet">
    <script>const ECAFE_BASE = '<?= e($config['url']) ?>';</script>
</head>
<body class="page-enter">
    <?php require ECAFE_ROOT . '/views/partials/navbar.php'; ?>

    <?php if ($flashSuccess): ?>
    <div class="container mt-3"><div class="alert alert-success alert-dismissible fade show"><?= e($flashSuccess) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
    <div class="container mt-3"><div class="alert alert-danger alert-dismissible fade show"><?= e($flashError) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>
    <?php endif; ?>

    <?= $content ?>

    <?php require ECAFE_ROOT . '/views/partials/footer.php'; ?>

    <div id="global-spinner"><div class="spinner-border text-light" style="width:3rem;height:3rem;"></div></div>
    <div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
