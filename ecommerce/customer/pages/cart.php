<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$items = cart_product_rows();
$totals = cart_totals();
require __DIR__ . '/../includes/header.php';
?>
<section class="page-head"><h1>Your Cart</h1></section>
<?php if (!$items): ?>
    <p>Your cart is empty.</p>
<?php else: ?>
    <div class="cart-list">
        <?php foreach ($items as $row): ?>
            <article class="cart-row">
                <span><?php echo e($row['product']['name']); ?></span>
                <span><?php echo e($row['quantity']); ?></span>
                <span><?php echo money($row['subtotal']); ?></span>
            </article>
        <?php endforeach; ?>
    </div>
    <aside class="summary">
        <p>Subtotal: <?php echo money($totals['subtotal']); ?></p>
        <p>Shipping: <?php echo money($totals['shipping']); ?></p>
        <p>Tax: <?php echo money($totals['tax']); ?></p>
        <strong>Total: <?php echo money($totals['total']); ?></strong>
        <a class="cta" href="checkout.php">Checkout</a>
    </aside>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
