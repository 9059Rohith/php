<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

$search = clean_text(request('q', ''));
$page = (int) request('page', 1);
$perPage = 10;
$params = [];
$sql = 'SELECT s.*, d.name AS department_name, c.name AS course_name FROM students s LEFT JOIN departments d ON d.id = s.department_id LEFT JOIN courses c ON c.id = s.course_id WHERE 1=1';

if ($search !== '') {
    $sql .= ' AND (s.name LIKE :search OR s.roll_no LIKE :search OR s.email LIKE :search OR d.name LIKE :search)';
    $params[':search'] = '%' . $search . '%';
}

$sql .= ' ORDER BY s.id DESC';
$result = paginate_query($sql, $params, $page, $perPage);

require __DIR__ . '/../../includes/header.php';
?>
<section class="panel">
    <div class="panel-head">
        <h2>Students</h2>
        <a class="button" href="/modules/students/add.php">Add Student</a>
    </div>
    <form method="get" class="search-bar">
        <input type="search" name="q" value="<?php echo e($search); ?>" placeholder="Search students...">
        <button type="submit">Search</button>
    </form>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Roll No</th><th>Name</th><th>Email</th><th>Department</th><th>Course</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($result['rows'] as $student): ?>
                <tr>
                    <td><?php echo e($student['roll_no']); ?></td>
                    <td><?php echo e($student['name']); ?></td>
                    <td><?php echo e($student['email']); ?></td>
                    <td><?php echo e($student['department_name']); ?></td>
                    <td><?php echo e($student['course_name']); ?></td>
                    <td><?php echo student_status_badge($student['status']); ?></td>
                    <td>
                        <a href="view.php?id=<?php echo e($student['id']); ?>">View</a>
                        <a href="edit.php?id=<?php echo e($student['id']); ?>">Edit</a>
                        <form method="post" action="delete.php" style="display:inline" onsubmit="return confirm('Delete this student?');">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo e($student['id']); ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
