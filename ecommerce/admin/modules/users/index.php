<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../admin/includes/bootstrap.php';
ecommerce_require_admin();

$users = pdo()->query('SELECT name, email, role, email_verified, created_at FROM users ORDER BY created_at DESC')->fetchAll();
?><!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Users</title></head><body><main style="padding:24px"><h1>Users</h1><table><?php foreach ($users as $user): ?><tr><td><?php echo e($user['name']); ?></td><td><?php echo e($user['email']); ?></td><td><?php echo e($user['role']); ?></td><td><?php echo e($user['email_verified']); ?></td></tr><?php endforeach; ?></table></main></body></html>
