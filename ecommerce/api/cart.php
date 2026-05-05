<?php
declare(strict_types=1);

require_once __DIR__ . '/../customer/includes/bootstrap.php';

if (request_method() === 'POST') {
    verify_csrf();
}

$action = clean_text(request('action', 'add'));
$productId = (int) request('product_id', 0);
$quantity = (int) request('quantity', 1);

if ($action === 'add') {
    cart_add($productId, $quantity);
    json_response(['success' => true, 'message' => 'Added to cart', 'count' => array_sum(cart_items())]);
}

if ($action === 'update') {
    cart_update($productId, $quantity);
    json_response(['success' => true, 'message' => 'Cart updated', 'count' => array_sum(cart_items())]);
}

if ($action === 'remove') {
    cart_remove($productId);
    json_response(['success' => true, 'message' => 'Item removed', 'count' => array_sum(cart_items())]);
}

json_response(['success' => false, 'message' => 'Unsupported action'], 400);
