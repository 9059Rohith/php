<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

if (request_method() !== 'POST') {
    redirect('/modules/students/index.php');
}

verify_csrf();

$id = (int) request('id');
$stmt = pdo()->prepare('UPDATE students SET status = "inactive" WHERE id = :id');
$stmt->execute([':id' => $id]);
activity_log((int) (current_user()['id'] ?? 0), 'deleted', 'student', $id);
flash('success', 'Student moved to inactive status.');
redirect('/student-management/modules/students/index.php');
