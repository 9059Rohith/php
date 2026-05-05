<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
event_require_roles(['admin', 'organizer', 'participant']);
if (request('action') === 'mark_all_read') {
    verify_csrf();
    pdo()->prepare('UPDATE notifications SET read_status = 1 WHERE user_id = :user_id')->execute([':user_id' => event_current_user()['id']]);
    json_response(['success' => true]);
}
json_response(['success' => false, 'message' => 'Unsupported action'], 400);
