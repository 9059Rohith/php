<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$user = ecommerce_current_user();
$orders = [];
if ($user) {
    $stmt = pdo()->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC');
    $stmt->execute([':user_id' => $user['id']]);
    $orders = $stmt->fetchAll();
}
require __DIR__ . '/../includes/header.php';
?>
<section class="page-head"><h1>My Account</h1></section>
<?php if (!$user): ?>
    <p>Sign in to see your orders.</p>
<?php else: ?>
    <p><?php echo e($user['name']); ?> · <?php echo e($user['email']); ?></p>
    <h2>Orders</h2>
    <div class="table-wrap"><table><thead><tr><th>Order</th><th>Status</th><th>Total</th></tr></thead><tbody><?php foreach ($orders as $order): ?><tr><td><?php echo e($order['order_number']); ?></td><td><?php echo e($order['status']); ?></td><td><?php echo money((float) $order['total_amount']); ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php endif; ?>
<?php require __DIR__ . '/../includes/footer.php'; ?>
