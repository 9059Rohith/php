<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function money(float $amount): string
{
    return '₹' . number_format($amount, 2);
}

function ecommerce_categories(): array
{
    return pdo()->query('SELECT id, name, slug, parent_id FROM categories WHERE status = "active" ORDER BY name')->fetchAll();
}

function ecommerce_featured_products(int $limit = 8): array
{
    $stmt = pdo()->prepare('SELECT id, name, slug, price, sale_price, stock_qty, images_json, rating_avg, rating_count FROM products WHERE status = "active" ORDER BY featured DESC, created_at DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll();
}

function ecommerce_product_by_slug(string $slug): ?array
{
    $stmt = pdo()->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id WHERE p.slug = :slug LIMIT 1');
    $stmt->execute([':slug' => $slug]);

    return $stmt->fetch() ?: null;
}

function ecommerce_product_image(array $product): string
{
    $images = json_decode((string) ($product['images_json'] ?? '[]'), true);
    return is_array($images) && isset($images[0]) ? (string) $images[0] : '/uploads/products/placeholder.svg';
}
