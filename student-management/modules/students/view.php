<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

$id = (int) request('id');
$stmt = pdo()->prepare('SELECT s.*, d.name AS department_name, c.name AS course_name FROM students s LEFT JOIN departments d ON d.id = s.department_id LEFT JOIN courses c ON c.id = s.course_id WHERE s.id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    http_response_code(404);
    echo 'Student not found';
    exit;
}

$marks = pdo()->prepare('SELECT * FROM marks WHERE student_id = :student_id ORDER BY year DESC, semester DESC');
$marks->execute([':student_id' => $id]);

$attendance = pdo()->prepare('SELECT * FROM attendance WHERE student_id = :student_id ORDER BY date DESC');
$attendance->execute([':student_id' => $id]);

require __DIR__ . '/../../includes/header.php';
?>
<section class="panel">
    <h2><?php echo e($student['name']); ?></h2>
    <p><?php echo e($student['roll_no']); ?> · <?php echo e($student['department_name']); ?> · <?php echo e($student['course_name']); ?></p>
    <p><?php echo e($student['email']); ?> | <?php echo e($student['phone']); ?></p>
    <p><?php echo e($student['address']); ?></p>
</section>

<section class="panel">
    <h3>Marks</h3>
    <div class="table-wrap"><table><thead><tr><th>Subject</th><th>Internal</th><th>External</th><th>Semester</th></tr></thead><tbody><?php foreach ($marks as $row): ?><tr><td><?php echo e($row['subject']); ?></td><td><?php echo e($row['internal_marks']); ?></td><td><?php echo e($row['external_marks']); ?></td><td><?php echo e($row['semester']); ?></td></tr><?php endforeach; ?></tbody></table></div>
</section>

<section class="panel">
    <h3>Attendance</h3>
    <div class="table-wrap"><table><thead><tr><th>Date</th><th>Subject</th><th>Status</th></tr></thead><tbody><?php foreach ($attendance as $row): ?><tr><td><?php echo e($row['date']); ?></td><td><?php echo e($row['subject']); ?></td><td><?php echo e($row['status']); ?></td></tr><?php endforeach; ?></tbody></table></div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
