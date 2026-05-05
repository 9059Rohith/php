<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';
require_login();

if (request_method() === 'POST') {
    verify_csrf();
    $stmt = pdo()->prepare('INSERT INTO students (roll_no, name, email, phone, dob, gender, address, photo, department_id, course_id, semester, admission_date, status) VALUES (:roll_no, :name, :email, :phone, :dob, :gender, :address, :photo, :department_id, :course_id, :semester, :admission_date, :status)');
    $stmt->execute([
        ':roll_no' => clean_text(request('roll_no')),
        ':name' => clean_text(request('name')),
        ':email' => clean_text(request('email')),
        ':phone' => clean_text(request('phone')),
        ':dob' => request('dob') ?: null,
        ':gender' => clean_text(request('gender')),
        ':address' => clean_text(request('address')),
        ':photo' => null,
        ':department_id' => (int) request('department_id'),
        ':course_id' => (int) request('course_id'),
        ':semester' => clean_text(request('semester')),
        ':admission_date' => request('admission_date'),
        ':status' => clean_text(request('status', 'active')),
    ]);
    activity_log((int) (current_user()['id'] ?? 0), 'added', 'student', (int) pdo()->lastInsertId());
    json_response(['success' => true, 'message' => 'Student added successfully.']);
}

$departments = pdo()->query('SELECT id, name FROM departments ORDER BY name')->fetchAll();
$courses = pdo()->query('SELECT id, name FROM courses ORDER BY name')->fetchAll();

require __DIR__ . '/../../includes/header.php';
?>
<section class="panel">
    <h2>Add Student</h2>
    <form method="post" data-ajax-form action="add.php" class="form-grid">
        <?php echo csrf_field(); ?>
        <input name="roll_no" placeholder="Roll No" required>
        <input name="name" placeholder="Full Name" required>
        <input name="email" type="email" placeholder="Email" required>
        <input name="phone" placeholder="Phone" required>
        <input name="dob" type="date">
        <select name="gender" required><option value="">Gender</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select>
        <textarea name="address" placeholder="Address" required></textarea>
        <select name="department_id" required><?php foreach ($departments as $department): ?><option value="<?php echo e($department['id']); ?>"><?php echo e($department['name']); ?></option><?php endforeach; ?></select>
        <select name="course_id" required><?php foreach ($courses as $course): ?><option value="<?php echo e($course['id']); ?>"><?php echo e($course['name']); ?></option><?php endforeach; ?></select>
        <input name="semester" placeholder="Semester" required>
        <input name="admission_date" type="date" required>
        <select name="status"><option value="active">Active</option><option value="inactive">Inactive</option></select>
        <button type="submit">Save</button>
    </form>
    <div data-toast-target></div>
</section>
<?php require __DIR__ . '/../../includes/footer.php'; ?>
