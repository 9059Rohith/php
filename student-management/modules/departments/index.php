<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

$departments = pdo()->query('SELECT d.*, COUNT(s.id) AS student_count FROM departments d LEFT JOIN students s ON s.department_id = d.id GROUP BY d.id ORDER BY d.name')->fetchAll();
require __DIR__ . '/../../includes/header.php';
?>
<section class="panel">
	<h2>Departments</h2>
	<div class="table-wrap">
		<table>
			<thead><tr><th>Name</th><th>Code</th><th>HOD</th><th>Students</th></tr></thead>
			<tbody>
			<?php foreach ($departments as $department): ?>
				<tr>
					<td><?php echo e($department['name']); ?></td>
					<td><?php echo e($department['code']); ?></td>
					<td><?php echo e($department['hod']); ?></td>
					<td><?php echo e($department['student_count']); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
