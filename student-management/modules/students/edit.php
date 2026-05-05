<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

$id = (int) request('id');
$stmt = pdo()->prepare('SELECT * FROM students WHERE id = :id');
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    http_response_code(404);
    echo 'Student not found';
    exit;
}

if (request_method() === 'POST') {
    verify_csrf();
    $stmt = pdo()->prepare('UPDATE students SET roll_no = :roll_no, name = :name, email = :email, phone = :phone, dob = :dob, gender = :gender, address = :address, department_id = :department_id, course_id = :course_id, semester = :semester, admission_date = :admission_date, status = :status WHERE id = :id');
    $stmt->execute([
        ':id' => $id,
        ':roll_no' => clean_text(request('roll_no')),
        ':name' => clean_text(request('name')),
        ':email' => clean_text(request('email')),
        ':phone' => clean_text(request('phone')),
        ':dob' => request('dob') ?: null,
        ':gender' => clean_text(request('gender')),
        ':address' => clean_text(request('address')),
        ':department_id' => (int) request('department_id'),
        ':course_id' => (int) request('course_id'),
        ':semester' => clean_text(request('semester')),
        ':admission_date' => request('admission_date'),
        ':status' => clean_text(request('status', 'active')),
    ]);
    activity_log((int) (current_user()['id'] ?? 0), 'edited', 'student', $id);
    json_response(['success' => true, 'message' => 'Student updated successfully.']);
}

$departments = pdo()->query('SELECT id, name FROM departments ORDER BY name')->fetchAll();
$courses = pdo()->query('SELECT id, name FROM courses ORDER BY name')->fetchAll();

require __DIR__ . '/../../includes/header.php';
?>
<section class="panel">
    <h2>Edit Student</h2>
    <form method="post" action="edit.php?id=<?php echo e($student['id']); ?>" class="form-grid">
        <?php echo csrf_field(); ?>
        <input name="roll_no" value="<?php echo e($student['roll_no']); ?>" required>
        <input name="name" value="<?php echo e($student['name']); ?>" required>
        <input name="email" type="email" value="<?php echo e($student['email']); ?>" required>
        <input name="phone" value="<?php echo e($student['phone']); ?>" required>
        <input name="dob" type="date" value="<?php echo e($student['dob']); ?>">
        <select name="gender" required>
            <?php foreach (['male', 'female', 'other'] as $gender): ?>
                <option value="<?php echo e($gender); ?>" <?php echo $student['gender'] === $gender ? 'selected' : ''; ?>><?php echo e(ucfirst($gender)); ?></option>
            <?php endforeach; ?>
        </select>
        <textarea name="address" required><?php echo e($student['address']); ?></textarea>
        <select name="department_id" required><?php foreach ($departments as $department): ?><option value="<?php echo e($department['id']); ?>" <?php echo (int) $student['department_id'] === (int) $department['id'] ? 'selected' : ''; ?>><?php echo e($department['name']); ?></option><?php endforeach; ?></select>
        <select name="course_id" required><?php foreach ($courses as $course): ?><option value="<?php echo e($course['id']); ?>" <?php echo (int) $student['course_id'] === (int) $course['id'] ? 'selected' : ''; ?>><?php echo e($course['name']); ?></option><?php endforeach; ?></select>
        <input name="semester" value="<?php echo e($student['semester']); ?>" required>
        <input name="admission_date" type="date" value="<?php echo e($student['admission_date']); ?>" required>
        <select name="status"><option value="active" <?php echo $student['status'] === 'active' ? 'selected' : ''; ?>>Active</option><option value="inactive" <?php echo $student['status'] === 'inactive' ? 'selected' : ''; ?>>Inactive</option></select>
        <button type="submit">Update</button>
    </form>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
