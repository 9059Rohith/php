<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function event_qr_url(string $payload): string
{
    return 'https://chart.googleapis.com/chart?chs=220x220&cht=qr&chl=' . urlencode($payload);
}

function event_by_slug(string $slug): ?array
{
    $stmt = pdo()->prepare('SELECT e.*, c.name AS category_name, u.name AS organizer_name FROM events e LEFT JOIN event_categories c ON c.id = e.category_id LEFT JOIN users u ON u.id = e.organizer_id WHERE e.slug = :slug LIMIT 1');
    $stmt->execute([':slug' => $slug]);

    return $stmt->fetch() ?: null;
}

function event_ticket_types(int $eventId): array
{
    $stmt = pdo()->prepare('SELECT * FROM ticket_types WHERE event_id = :event_id ORDER BY price ASC');
    $stmt->execute([':event_id' => $eventId]);

    return $stmt->fetchAll();
}

function event_registration_number(): string
{
    return 'REG' . date('YmdHis') . random_int(100, 999);
}
