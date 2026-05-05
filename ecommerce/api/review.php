<?php
declare(strict_types=1);

require_once __DIR__ . '/../customer/includes/bootstrap.php';
$user = ecommerce_current_user();
if (!$user) {
    json_response(['success' => false, 'message' => 'Login required'], 401);
}

verify_csrf();
$stmt = pdo()->prepare('INSERT INTO product_reviews (product_id, user_id, rating, review, created_at) VALUES (:product_id, :user_id, :rating, :review, NOW())');
$stmt->execute([
    ':product_id' => (int) request('product_id', 0),
    ':user_id' => $user['id'],
    ':rating' => (int) request('rating', 5),
    ':review' => clean_text(request('review', '')),
]);
json_response(['success' => true, 'message' => 'Review saved']);
