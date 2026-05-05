<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../admin/includes/bootstrap.php';
ecommerce_require_admin();

$coupons = pdo()->query('SELECT code, type, value, used_count, usage_limit, status FROM coupons ORDER BY id DESC')->fetchAll();
?><!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Coupons</title></head><body><main style="padding:24px"><h1>Coupons</h1><table><?php foreach ($coupons as $coupon): ?><tr><td><?php echo e($coupon['code']); ?></td><td><?php echo e($coupon['type']); ?></td><td><?php echo e($coupon['value']); ?></td><td><?php echo e($coupon['used_count']); ?>/<?php echo e($coupon['usage_limit'] ?? '∞'); ?></td></tr><?php endforeach; ?></table></main></body></html>
