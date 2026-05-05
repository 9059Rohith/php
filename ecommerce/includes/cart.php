<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function cart_items(): array
{
    return $_SESSION['cart']['items'] ?? [];
}

function cart_add(int $productId, int $quantity = 1): void
{
    $items = cart_items();
    $items[$productId] = ($items[$productId] ?? 0) + max(1, $quantity);
    $_SESSION['cart']['items'] = $items;
}

function cart_update(int $productId, int $quantity): void
{
    $items = cart_items();
    if ($quantity <= 0) {
        unset($items[$productId]);
    } else {
        $items[$productId] = $quantity;
    }
    $_SESSION['cart']['items'] = $items;
}

function cart_remove(int $productId): void
{
    $items = cart_items();
    unset($items[$productId]);
    $_SESSION['cart']['items'] = $items;
}

function cart_product_rows(): array
{
    $ids = array_keys(cart_items());
    if (!$ids) {
        return [];
    }

    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = pdo()->prepare('SELECT id, name, price, sale_price, stock_qty, images_json FROM products WHERE id IN (' . $placeholders . ')');
    $stmt->execute($ids);

    $rows = [];
    foreach ($stmt->fetchAll() as $product) {
        $quantity = (int) (cart_items()[$product['id']] ?? 0);
        $unitPrice = (float) ($product['sale_price'] ?? 0) > 0 ? (float) $product['sale_price'] : (float) $product['price'];
        $rows[] = [
            'product' => $product,
            'quantity' => $quantity,
            'subtotal' => $quantity * $unitPrice,
        ];
    }

    return $rows;
}

function cart_totals(): array
{
    $subtotal = 0.0;
    foreach (cart_product_rows() as $row) {
        $subtotal += $row['subtotal'];
    }
    $shipping = $subtotal > 0 ? 49.00 : 0.00;
    $tax = round($subtotal * 0.18, 2);
    $total = round($subtotal + $shipping + $tax, 2);

    return compact('subtotal', 'shipping', 'tax', 'total');
}
