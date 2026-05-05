<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$user = event_current_user();
if (!$user) {
    json_response(['success' => false, 'message' => 'Login required'], 401);
}

verify_csrf();
$eventId = (int) request('event_id', 0);
$action = clean_text(request('action', 'join'));
if ($action === 'leave') {
    pdo()->prepare('DELETE FROM waitlist WHERE event_id = :event_id AND user_id = :user_id')->execute([':event_id' => $eventId, ':user_id' => $user['id']]);
    json_response(['success' => true, 'state' => 'left']);
}

$stmt = pdo()->prepare('SELECT COALESCE(MAX(position),0) AS max_position FROM waitlist WHERE event_id = :event_id');
$stmt->execute([':event_id' => $eventId]);
$position = ((int) ($stmt->fetch()['max_position'] ?? 0)) + 1;
pdo()->prepare('INSERT INTO waitlist (event_id, user_id, position, joined_at, notified) VALUES (:event_id, :user_id, :position, NOW(), 0)')->execute([':event_id' => $eventId, ':user_id' => $user['id'], ':position' => $position]);
json_response(['success' => true, 'state' => 'joined', 'position' => $position]);
