<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$category = clean_text(request('category', ''));
$sort = clean_text(request('sort', 'newest'));
$sql = 'SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.stock_qty, p.images_json FROM products p WHERE p.status = "active"';
$params = [];

if ($category !== '') {
    $sql .= ' AND p.category_id = :category_id';
    $params[':category_id'] = (int) $category;
}

$sql .= match ($sort) {
    'price_low' => ' ORDER BY COALESCE(p.sale_price, p.price) ASC',
    'price_high' => ' ORDER BY COALESCE(p.sale_price, p.price) DESC',
    'popular' => ' ORDER BY p.rating_count DESC, p.created_at DESC',
    default => ' ORDER BY p.created_at DESC',
};

$stmt = pdo()->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
$categories = ecommerce_categories();

require __DIR__ . '/../includes/header.php';
?>
<section class="page-head"><h1>Catalog</h1><p>Filter by category and sort by price or popularity.</p></section>
<form method="get" class="filters">
    <select name="category"><option value="">All categories</option><?php foreach ($categories as $item): ?><option value="<?php echo e($item['id']); ?>" <?php echo (string) $category === (string) $item['id'] ? 'selected' : ''; ?>><?php echo e($item['name']); ?></option><?php endforeach; ?></select>
    <select name="sort"><option value="newest">Newest</option><option value="price_low">Price low</option><option value="price_high">Price high</option><option value="popular">Popularity</option></select>
    <button type="submit">Apply</button>
</form>
<section class="grid products-grid">
    <?php foreach ($products as $product): ?>
        <article class="card product-card">
            <img src="<?php echo e(ecommerce_product_image($product)); ?>" alt="<?php echo e($product['name']); ?>">
            <h3><?php echo e($product['name']); ?></h3>
            <p><?php echo money((float) ($product['sale_price'] ?: $product['price'])); ?></p>
            <button class="add-cart" data-product-id="<?php echo e($product['id']); ?>">Add to Cart</button>
            <a href="product.php?slug=<?php echo e($product['slug']); ?>">Details</a>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
