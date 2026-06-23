<?php
$role = $dashboardRole ?? 'student';
$menus = [
    'student' => [
        ['icon' => 'fa-home', 'label' => 'Dashboard', 'url' => '/student/dashboard'],
        ['icon' => 'fa-book-open', 'label' => 'Menu', 'url' => '/student/menu'],
        ['icon' => 'fa-shopping-cart', 'label' => 'Cart', 'url' => '/student/cart'],
        ['icon' => 'fa-receipt', 'label' => 'My Orders', 'url' => '/student/orders'],
        ['icon' => 'fa-bell', 'label' => 'Notifications', 'url' => '/student/notifications'],
        ['icon' => 'fa-user', 'label' => 'Profile', 'url' => '/student/profile'],
    ],
    'staff' => [
        ['icon' => 'fa-home', 'label' => 'Dashboard', 'url' => '/staff/dashboard'],
        ['icon' => 'fa-clipboard-list', 'label' => 'Orders', 'url' => '/staff/orders'],
        ['icon' => 'fa-boxes', 'label' => 'Inventory', 'url' => '/staff/inventory'],
        ['icon' => 'fa-chart-bar', 'label' => 'Reports', 'url' => '/staff/reports'],
    ],
    'admin' => [
        ['icon' => 'fa-home', 'label' => 'Dashboard', 'url' => '/admin/dashboard'],
        ['icon' => 'fa-user-graduate', 'label' => 'Students', 'url' => '/admin/students'],
        ['icon' => 'fa-users', 'label' => 'Staff', 'url' => '/admin/staff'],
        ['icon' => 'fa-utensils', 'label' => 'Menu', 'url' => '/admin/menu'],
        ['icon' => 'fa-tags', 'label' => 'Categories', 'url' => '/admin/categories'],
        ['icon' => 'fa-receipt', 'label' => 'Orders', 'url' => '/admin/orders'],
        ['icon' => 'fa-credit-card', 'label' => 'Payments', 'url' => '/admin/payments'],
        ['icon' => 'fa-bullhorn', 'label' => 'Announcements', 'url' => '/admin/announcements'],
        ['icon' => 'fa-chart-line', 'label' => 'Reports', 'url' => '/admin/reports'],
        ['icon' => 'fa-cog', 'label' => 'Settings', 'url' => '/admin/settings'],
    ],
];
$items = $menus[$role] ?? $menus['student'];
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>
<aside class="sidebar">
    <div class="sidebar-brand"><i class="fas fa-utensils me-2"></i>e-<span class="accent">Café</span></div>
    <nav class="sidebar-nav">
        <?php foreach ($items as $item): ?>
        <a href="<?= url($item['url']) ?>" class="sidebar-link <?= str_contains($currentPath, $item['url']) ? 'active' : '' ?>">
            <i class="fas <?= $item['icon'] ?>"></i> <?= e($item['label']) ?>
        </a>
        <?php endforeach; ?>
        <a href="<?= url('/logout') ?>" class="sidebar-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
</aside>
