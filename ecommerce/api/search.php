<?php
declare(strict_types=1);

require_once __DIR__ . '/../customer/includes/bootstrap.php';
$term = '%' . clean_text(request('q', '')) . '%';
$stmt = pdo()->prepare('SELECT id, name, slug FROM products WHERE name LIKE :term OR sku LIKE :term LIMIT 8');
$stmt->execute([':term' => $term]);
json_response(['success' => true, 'items' => $stmt->fetchAll()]);
