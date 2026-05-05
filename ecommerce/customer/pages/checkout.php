<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$totals = cart_totals();

if (request_method() === 'POST') {
    verify_csrf();
    $user = ecommerce_current_user();
    $stmt = pdo()->prepare('INSERT INTO orders (user_id, order_number, status, total_amount, discount_amount, shipping_amount, tax_amount, payment_method, payment_status, notes, created_at) VALUES (:user_id, :order_number, :status, :total_amount, 0, :shipping_amount, :tax_amount, :payment_method, :payment_status, :notes, NOW())');
    $orderNumber = 'ORD' . date('YmdHis') . random_int(100, 999);
    $stmt->execute([
        ':user_id' => $user['id'] ?? null,
        ':order_number' => $orderNumber,
        ':status' => 'pending',
        ':total_amount' => $totals['total'],
        ':shipping_amount' => $totals['shipping'],
        ':tax_amount' => $totals['tax'],
        ':payment_method' => clean_text(request('payment_method', 'cod')),
        ':payment_status' => 'pending',
        ':notes' => clean_text(request('notes', '')),
    ]);
    $orderId = (int) pdo()->lastInsertId();

    foreach (cart_product_rows() as $row) {
        $product = $row['product'];
        $unitPrice = (float) ($product['sale_price'] ?: $product['price']);
        pdo()->prepare('INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) VALUES (:order_id, :product_id, :quantity, :price, :subtotal)')->execute([
            ':order_id' => $orderId,
            ':product_id' => $product['id'],
            ':quantity' => $row['quantity'],
            ':price' => $unitPrice,
            ':subtotal' => $row['subtotal'],
        ]);
    }

    $_SESSION['cart'] = ['items' => []];
    redirect('/customer/pages/account.php?placed=' . urlencode($orderNumber));
}

require __DIR__ . '/../includes/header.php';
?>
<section class="page-head"><h1>Checkout</h1></section>
<form method="post" class="checkout-form">
    <?php echo csrf_field(); ?>
    <label>Payment Method<select name="payment_method"><option value="cod">Cash on Delivery</option><option value="razorpay">Razorpay Test</option></select></label>
    <label>Notes<textarea name="notes"></textarea></label>
    <p>Total to pay: <?php echo money($totals['total']); ?></p>
    <button type="submit">Place Order</button>
</form>
<?php require __DIR__ . '/../includes/footer.php'; ?>
