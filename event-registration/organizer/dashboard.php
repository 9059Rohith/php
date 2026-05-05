<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$user = event_current_user();
event_require_roles(['admin', 'organizer']);
$stmt = pdo()->prepare('SELECT COUNT(*) AS total FROM events WHERE organizer_id = :organizer_id');
$stmt->execute([':organizer_id' => $user['id']]);
$totalEvents = (int) ($stmt->fetch()['total'] ?? 0);
$registrations = pdo()->prepare('SELECT COUNT(*) AS total FROM registrations r INNER JOIN events e ON e.id = r.event_id WHERE e.organizer_id = :organizer_id');
$registrations->execute([':organizer_id' => $user['id']]);
$totalRegistrations = (int) ($registrations->fetch()['total'] ?? 0);
$events = pdo()->prepare('SELECT title, status, event_date FROM events WHERE organizer_id = :organizer_id ORDER BY created_at DESC LIMIT 5');
$events->execute([':organizer_id' => $user['id']]);
?><!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" href="/event-registration/assets/app.css"><title>Organizer Dashboard</title></head><body><main class="panel"><h1>Organizer Dashboard</h1><section class="cards-grid"><article class="event-card"><h3>Events</h3><strong><?php echo e($totalEvents); ?></strong></article><article class="event-card"><h3>Registrations</h3><strong><?php echo e($totalRegistrations); ?></strong></article></section><section class="panel" style="margin-top:16px;"><h2>Recent events</h2><?php foreach ($events as $event): ?><p><?php echo e($event['title']); ?> · <?php echo e($event['status']); ?> · <?php echo e($event['event_date']); ?></p><?php endforeach; ?></section></main></body></html>
