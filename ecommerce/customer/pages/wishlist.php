<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$user = ecommerce_current_user();
$items = [];
if ($user) {
    $stmt = pdo()->prepare('SELECT p.* FROM wishlist w INNER JOIN products p ON p.id = w.product_id WHERE w.user_id = :user_id');
    $stmt->execute([':user_id' => $user['id']]);
    $items = $stmt->fetchAll();
}
require __DIR__ . '/../includes/header.php';
?>
<section class="page-head"><h1>Wishlist</h1></section>
<?php foreach ($items as $product): ?><p><?php echo e($product['name']); ?></p><?php endforeach; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
