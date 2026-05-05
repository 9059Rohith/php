<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../admin/includes/bootstrap.php';
ecommerce_require_admin();
$products = pdo()->query('SELECT id, name, sku, price, stock_qty, status FROM products ORDER BY created_at DESC')->fetchAll();
?><!doctype html><html><head><meta charset="UTF-8"><title>Products</title></head><body><h1>Products</h1><a href="add.php">Add</a><table><?php foreach ($products as $product): ?><tr><td><?php echo e($product['name']); ?></td><td><?php echo e($product['sku']); ?></td><td><?php echo money((float) $product['price']); ?></td><td><?php echo e($product['stock_qty']); ?></td><td><?php echo e($product['status']); ?></td></tr><?php endforeach; ?></table></body></html>
