<?php
declare(strict_types=1);

require_once __DIR__ . '/../customer/includes/bootstrap.php';
$user = ecommerce_current_user();
if (!$user) {
    json_response(['success' => false, 'message' => 'Login required'], 401);
}

$productId = (int) request('product_id', 0);
$stmt = pdo()->prepare('SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id LIMIT 1');
$stmt->execute([':user_id' => $user['id'], ':product_id' => $productId]);

if ($stmt->fetch()) {
    pdo()->prepare('DELETE FROM wishlist WHERE user_id = :user_id AND product_id = :product_id')->execute([':user_id' => $user['id'], ':product_id' => $productId]);
    json_response(['success' => true, 'state' => false]);
}

pdo()->prepare('INSERT INTO wishlist (user_id, product_id, created_at) VALUES (:user_id, :product_id, NOW())')->execute([':user_id' => $user['id'], ':product_id' => $productId]);
json_response(['success' => true, 'state' => true]);
