<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

$users = pdo()->query('SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC')->fetchAll();
require __DIR__ . '/../../includes/header.php';
?>
<section class="panel">
	<h2>Users</h2>
	<div class="table-wrap">
		<table>
			<thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th></tr></thead>
			<tbody>
			<?php foreach ($users as $user): ?>
				<tr>
					<td><?php echo e($user['name']); ?></td>
					<td><?php echo e($user['email']); ?></td>
					<td><?php echo e($user['role']); ?></td>
					<td><?php echo e($user['status']); ?></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
