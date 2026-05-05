<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

$courses = pdo()->query('SELECT c.*, d.name AS department_name FROM courses c LEFT JOIN departments d ON d.id = c.department_id ORDER BY c.name')->fetchAll();
require __DIR__ . '/../../includes/header.php';
?>
<section class="panel">
	<h2>Courses</h2>
	<div class="table-wrap">
		<table>
			<thead><tr><th>Name</th><th>Department</th><th>Duration</th></tr></thead>
			<tbody>
			<?php foreach ($courses as $course): ?>
				<tr>
					<td><?php echo e($course['name']); ?></td>
					<td><?php echo e($course['department_name']); ?></td>
					<td><?php echo e($course['duration_years']); ?> years</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
