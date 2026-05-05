<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect('/index.php');
}

if (request_method() === 'POST') {
    verify_csrf();

    $name = clean_text(request('name', ''));
    $email = clean_text(request('email', ''));
    $password = (string) request('password', '');
    $password_confirm = (string) request('password_confirm', '');

    if ($name === '' || $email === '' || $password === '') {
        flash('error', 'All fields are required.');
        redirect('/register.php');
    }

    if ($password !== $password_confirm) {
        flash('error', 'Passwords do not match.');
        redirect('/register.php');
    }

    if (strlen($password) < 6) {
        flash('error', 'Password must be at least 6 characters long.');
        redirect('/register.php');
    }

    // Check if email already exists
    $stmt = pdo()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        flash('error', 'Email is already registered.');
        redirect('/register.php');
    }

    // Register new user
    $password_hash = password_hash_for_user($password);
    $role = 'student'; // Default role for new registrations

    try {
        $stmt = pdo()->prepare('INSERT INTO users (name, email, password_hash, role, status) VALUES (:name, :email, :password_hash, :role, :status)');
        $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password_hash' => $password_hash,
            ':role' => $role,
            ':status' => 'active',
        ]);

        flash('success', 'Registration successful! You can now login.');
        redirect('/login.php');
    } catch (Exception $e) {
        flash('error', 'Registration failed. Please try again.');
        redirect('/register.php');
    }
}
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register · <?php echo e(app_name()); ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="auth-page">
<section class="auth-card">
    <h1>Register</h1>
    <p>Create a new account to get started.</p>
    <?php if ($message = flash('error')): ?><div class="toast error"><?php echo e($message); ?></div><?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <label>Full Name<input type="text" name="name" required></label>
        <label>Email<input type="email" name="email" required></label>
        <label>Password<input type="password" name="password" required></label>
        <label>Confirm Password<input type="password" name="password_confirm" required></label>
        <button type="submit">Register</button>
    </form>
    <p>Already have an account? <a href="/login.php">Sign in here</a></p>
</section>
</body>
</html>
