<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../admin/includes/bootstrap.php';
ecommerce_require_admin();

$categories = pdo()->query('SELECT c.*, COUNT(p.id) AS product_count FROM categories c LEFT JOIN products p ON p.category_id = c.id GROUP BY c.id ORDER BY c.name')->fetchAll();
?><!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Categories</title></head><body><main style="padding:24px"><h1>Categories</h1><table><?php foreach ($categories as $category): ?><tr><td><?php echo e($category['name']); ?></td><td><?php echo e($category['slug']); ?></td><td><?php echo e($category['product_count']); ?></td></tr><?php endforeach; ?></table></main></body></html>
