<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/bootstrap.php';
ecommerce_require_admin();

$stats = [
    'revenue' => (float) (pdo()->query('SELECT COALESCE(SUM(total_amount),0) AS total FROM orders')->fetch()['total'] ?? 0),
    'orders' => (int) (pdo()->query('SELECT COUNT(*) AS total FROM orders')->fetch()['total'] ?? 0),
    'products' => (int) (pdo()->query('SELECT COUNT(*) AS total FROM products')->fetch()['total'] ?? 0),
    'low_stock' => (int) (pdo()->query('SELECT COUNT(*) AS total FROM products WHERE stock_qty < 5')->fetch()['total'] ?? 0),
];
$recentOrders = pdo()->query('SELECT order_number, status, total_amount, created_at FROM orders ORDER BY created_at DESC LIMIT 5')->fetchAll();
$topProducts = pdo()->query('SELECT name, stock_qty, rating_avg FROM products ORDER BY rating_count DESC, created_at DESC LIMIT 5')->fetchAll();
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/ecommerce/admin/assets/admin.css">
    <title>E-Commerce Admin</title>
</head>
<body>
<main class="admin-shell">
    <header class="admin-hero">
        <div>
            <p class="eyebrow">Admin Console</p>
            <h1>E-Commerce Dashboard</h1>
            <p>Revenue, order volume, product health, and operational shortcuts in one place.</p>
        </div>
        <nav class="admin-actions">
            <a href="/ecommerce/customer/pages/home.php">View Storefront</a>
            <a href="/ecommerce/admin/modules/products/index.php">Manage Products</a>
            <a href="/ecommerce/admin/modules/orders/index.php">Review Orders</a>
        </nav>
    </header>

    <section class="admin-grid">
        <article class="metric-card">Revenue <strong><?php echo money($stats['revenue']); ?></strong></article>
        <article class="metric-card">Orders <strong><?php echo e($stats['orders']); ?></strong></article>
        <article class="metric-card">Products <strong><?php echo e($stats['products']); ?></strong></article>
        <article class="metric-card">Low stock <strong><?php echo e($stats['low_stock']); ?></strong></article>
    </section>

    <section class="admin-panels">
        <article class="panel-card">
            <h2>Recent Orders</h2>
            <ul>
                <?php foreach ($recentOrders as $order): ?>
                    <li><?php echo e($order['order_number']); ?> · <?php echo e($order['status']); ?> · <?php echo money((float) $order['total_amount']); ?></li>
                <?php endforeach; ?>
            </ul>
        </article>
        <article class="panel-card">
            <h2>Top Products</h2>
            <ul>
                <?php foreach ($topProducts as $product): ?>
                    <li><?php echo e($product['name']); ?> · Stock <?php echo e($product['stock_qty']); ?> · Rating <?php echo e($product['rating_avg']); ?></li>
                <?php endforeach; ?>
            </ul>
        </article>
    </section>
</main>
</body>
</html>
