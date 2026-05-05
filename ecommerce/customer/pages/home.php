<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/header.php';
$products = ecommerce_featured_products();
?>
<section class="hero">
    <h1>Clean storefront, fast cart, simple checkout.</h1>
    <p>Core PHP ecommerce with AJAX cart actions and admin catalog management.</p>
    <a class="cta" href="listing.php">Browse products</a>
</section>
<section class="grid products-grid">
    <?php foreach ($products as $product): ?>
        <article class="card product-card">
            <img src="<?php echo e(ecommerce_product_image($product)); ?>" alt="<?php echo e($product['name']); ?>">
            <h3><?php echo e($product['name']); ?></h3>
            <p><?php echo money((float) ($product['sale_price'] ?: $product['price'])); ?></p>
            <a href="product.php?slug=<?php echo e($product['slug']); ?>">View</a>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
