<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
$user = ecommerce_current_user();
$cartCount = array_sum(cart_items());
$success = flash('success');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(app_name()); ?></title>
    <link rel="stylesheet" href="/customer/assets/app.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="/customer/pages/home.php">ShopVerse</a>
    <nav>
        <a href="/customer/pages/listing.php">Catalog</a>
        <a href="/customer/pages/wishlist.php">Wishlist</a>
        <a href="/customer/pages/cart.php">Cart <span class="cart-badge"><?php echo e($cartCount); ?></span></a>
        <a href="/customer/pages/account.php">Account</a>
        <?php if ($user): ?><a href="/customer/pages/account.php"><?php echo e($user['name']); ?></a><?php else: ?><a href="/customer/pages/login.php">Login</a><?php endif; ?>
    </nav>
</header>
<?php if ($success): ?>
    <div class="success-message"><?php echo e($success); ?></div>
<?php endif; ?>
<main class="site-main">
