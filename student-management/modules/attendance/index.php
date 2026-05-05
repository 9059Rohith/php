<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

$attendance = pdo()->query('SELECT a.*, s.name AS student_name FROM attendance a LEFT JOIN students s ON s.id = a.student_id ORDER BY a.date DESC')->fetchAll();
require __DIR__ . '/../../includes/header.php';
?>
<section class="panel">
	<h2>Attendance</h2>
	<div class="table-wrap">
		<table>
			<thead><tr><th>Date</th><th>Student</th><th>Subject</th><th>Status</th></tr></thead>
			<tbody>
			<?php foreach ($attendance as $row): ?>
				<tr>
					<td><?php echo e($row['date']); ?></td>
					<td><?php echo e($row['student_name']); ?></td>
					<td><?php echo e($row['subject']); ?></td>
					<td><?php echo e($row['status']); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
