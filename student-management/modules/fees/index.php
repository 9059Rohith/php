<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

$fees = pdo()->query('SELECT f.*, s.name AS student_name FROM fees f LEFT JOIN students s ON s.id = f.student_id ORDER BY f.due_date DESC')->fetchAll();
require __DIR__ . '/../../includes/header.php';
?>
<section class="panel">
	<h2>Fees</h2>
	<div class="table-wrap">
		<table>
			<thead><tr><th>Student</th><th>Amount</th><th>Due Date</th><th>Status</th><th>Transaction</th></tr></thead>
			<tbody>
			<?php foreach ($fees as $fee): ?>
				<tr>
					<td><?php echo e($fee['student_name']); ?></td>
					<td><?php echo e($fee['amount']); ?></td>
					<td><?php echo e($fee['due_date']); ?></td>
					<td><?php echo e($fee['status']); ?></td>
					<td><?php echo e($fee['transaction_id'] ?? '-'); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
