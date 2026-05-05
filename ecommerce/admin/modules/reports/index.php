<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../admin/includes/bootstrap.php';
ecommerce_require_admin();

$sales = pdo()->query('SELECT DATE(created_at) AS day, SUM(total_amount) AS total FROM orders GROUP BY DATE(created_at) ORDER BY day DESC LIMIT 10')->fetchAll();
?><!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Reports</title></head><body><main style="padding:24px"><h1>Reports</h1><table><?php foreach ($sales as $row): ?><tr><td><?php echo e($row['day']); ?></td><td><?php echo money((float) $row['total']); ?></td></tr><?php endforeach; ?></table></main></body></html>
