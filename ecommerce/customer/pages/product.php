<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$product = ecommerce_product_by_slug(clean_text(request('slug', '')));

if (!$product) {
    http_response_code(404);
    echo 'Product not found';
    exit;
}

require __DIR__ . '/../includes/header.php';
?>
<section class="product-detail">
    <img src="<?php echo e(ecommerce_product_image($product)); ?>" alt="<?php echo e($product['name']); ?>">
    <div>
        <h1><?php echo e($product['name']); ?></h1>
        <p class="price"><?php echo money((float) ($product['sale_price'] ?: $product['price'])); ?></p>
        <p><?php echo e($product['description']); ?></p>
        <form method="post" action="/api/cart.php">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="action" value="add">
            <input type="hidden" name="product_id" value="<?php echo e($product['id']); ?>">
            <input type="number" name="quantity" value="1" min="1">
            <button type="submit">Add to Cart</button>
        </form>
    </div>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
