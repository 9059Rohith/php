<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../admin/includes/bootstrap.php';
ecommerce_require_admin();

$orders = pdo()->query('SELECT order_number, status, total_amount, payment_status, created_at FROM orders ORDER BY created_at DESC')->fetchAll();
?><!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Orders</title></head><body><main style="padding:24px"><h1>Orders</h1><table><?php foreach ($orders as $order): ?><tr><td><?php echo e($order['order_number']); ?></td><td><?php echo e($order['status']); ?></td><td><?php echo money((float) $order['total_amount']); ?></td><td><?php echo e($order['payment_status']); ?></td></tr><?php endforeach; ?></table></main></body></html>
