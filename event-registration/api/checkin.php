<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
event_require_roles(['admin', 'organizer']);
verify_csrf();
$registrationNumber = clean_text(request('registration_number', ''));
pdo()->prepare('UPDATE registrations SET check_in_status = "checked_in", check_in_time = NOW() WHERE registration_number = :registration_number')->execute([':registration_number' => $registrationNumber]);
json_response(['success' => true, 'message' => 'Checked in']);
