<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/functions.php';

if (request_method() === 'POST') {
    verify_csrf();
}

$action = (string) request('action', 'list');

if ($action === 'search') {
    $term = '%' . clean_text(request('term', '')) . '%';
    $stmt = pdo()->prepare('SELECT id, roll_no, name, email FROM students WHERE name LIKE :term OR roll_no LIKE :term OR email LIKE :term LIMIT 10');
    $stmt->execute([':term' => $term]);
    json_response(['success' => true, 'data' => $stmt->fetchAll()]);
}

if ($action === 'save') {
    $payload = [
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
    ];

    $id = (int) request('id', 0);

    if ($id > 0) {
        $payload[':id'] = $id;
        $stmt = pdo()->prepare('UPDATE students SET roll_no = :roll_no, name = :name, email = :email, phone = :phone, dob = :dob, gender = :gender, address = :address, photo = :photo, department_id = :department_id, course_id = :course_id, semester = :semester, admission_date = :admission_date, status = :status WHERE id = :id');
        $stmt->execute($payload);
        activity_log((int) (current_user()['id'] ?? 0), 'edited', 'student', $id);
    } else {
        $stmt = pdo()->prepare('INSERT INTO students (roll_no, name, email, phone, dob, gender, address, photo, department_id, course_id, semester, admission_date, status) VALUES (:roll_no, :name, :email, :phone, :dob, :gender, :address, :photo, :department_id, :course_id, :semester, :admission_date, :status)');
        $stmt->execute($payload);
        $id = (int) pdo()->lastInsertId();
        activity_log((int) (current_user()['id'] ?? 0), 'added', 'student', $id);
    }

    json_response(['success' => true, 'message' => 'Student saved successfully.', 'id' => $id]);
}

if ($action === 'delete') {
    $id = (int) request('id');
    $stmt = pdo()->prepare('UPDATE students SET status = "inactive" WHERE id = :id');
    $stmt->execute([':id' => $id]);
    activity_log((int) (current_user()['id'] ?? 0), 'deleted', 'student', $id);
    json_response(['success' => true, 'message' => 'Student inactivated.']);
}

json_response(['success' => false, 'message' => 'Unsupported action.'], 400);
