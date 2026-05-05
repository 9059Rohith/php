<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$user = event_current_user();
event_require_roles(['admin', 'organizer', 'participant']);
$stmt = pdo()->prepare('SELECT r.*, e.title AS event_title, e.event_date FROM registrations r LEFT JOIN events e ON e.id = r.event_id WHERE r.user_id = :user_id ORDER BY r.registered_at DESC');
$stmt->execute([':user_id' => $user['id']]);
$registrations = $stmt->fetchAll();
?><!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><link rel="stylesheet" href="/event-registration/assets/app.css"><title>Participant Dashboard</title></head><body><main class="panel"><h1>My Registrations</h1><section class="cards-grid"><?php foreach ($registrations as $registration): ?><article class="event-card"><h3><?php echo e($registration['event_title']); ?></h3><p><?php echo e($registration['registration_number']); ?></p><p><?php echo e($registration['event_date']); ?></p></article><?php endforeach; ?></section></main></body></html>
