<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$term = '%' . clean_text(request('q', '')) . '%';
$stmt = pdo()->prepare('SELECT id, title, slug, city FROM events WHERE title LIKE :term OR city LIKE :term OR venue LIKE :term LIMIT 10');
$stmt->execute([':term' => $term]);
json_response(['success' => true, 'items' => $stmt->fetchAll()]);
