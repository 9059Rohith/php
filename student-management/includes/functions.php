<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function student_status_badge(string $status): string
{
    $class = match ($status) {
        'active' => 'badge-success',
        'inactive' => 'badge-danger',
        default => 'badge-secondary',
    };

    return '<span class="badge ' . $class . '">' . e(ucfirst($status)) . '</span>';
}

function activity_log(int $userId, string $action, string $entity, int $entityId): void
{
    $stmt = pdo()->prepare('INSERT INTO activity_log (user_id, action, entity, entity_id, timestamp) VALUES (:user_id, :action, :entity, :entity_id, NOW())');
    $stmt->execute([
        ':user_id' => $userId,
        ':action' => $action,
        ':entity' => $entity,
        ':entity_id' => $entityId,
    ]);
}

function student_metrics(): array
{
    $pdo = pdo();

    return [
        'students' => (int) $pdo->query('SELECT COUNT(*) AS total FROM students')->fetch()['total'],
        'active_students' => (int) $pdo->query("SELECT COUNT(*) AS total FROM students WHERE status = 'active'")->fetch()['total'],
        'departments' => (int) $pdo->query('SELECT COUNT(*) AS total FROM departments')->fetch()['total'],
        'pending_fees' => (int) $pdo->query("SELECT COUNT(*) AS total FROM fees WHERE status <> 'paid'")->fetch()['total'],
    ];
}
