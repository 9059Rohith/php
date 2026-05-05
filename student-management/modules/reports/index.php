<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

$stats = [
	'total_students' => student_metrics()['students'],
	'attendance_rows' => (int) pdo()->query('SELECT COUNT(*) AS total FROM attendance')->fetch()['total'],
	'fee_rows' => (int) pdo()->query('SELECT COUNT(*) AS total FROM fees')->fetch()['total'],
];
require __DIR__ . '/../../includes/header.php';
?>
<section class="dashboard-grid">
	<article class="stat-card"><span>Total Students</span><strong><?php echo e($stats['total_students']); ?></strong></article>
	<article class="stat-card"><span>Attendance Logs</span><strong><?php echo e($stats['attendance_rows']); ?></strong></article>
	<article class="stat-card"><span>Fee Records</span><strong><?php echo e($stats['fee_rows']); ?></strong></article>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>

