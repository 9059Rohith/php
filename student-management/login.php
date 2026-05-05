<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/auth.php';

if (current_user()) {
    redirect('/index.php');
}

if (request_method() === 'POST') {
    verify_csrf();

    $email = clean_text(request('email', ''));
    $password = (string) request('password', '');

    if ($email === '' || $password === '') {
        flash('error', 'Email and password are required.');
        redirect('/login.php');
    }

    if (attempt_login($email, $password)) {
        flash('success', 'Welcome back.');
        redirect('/index.php');
    }

    flash('error', 'Invalid credentials.');
    redirect('/login.php');
}
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login · <?php echo e(app_name()); ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="auth-page">
<section class="auth-card">
    <h1>Login</h1>
    <p>Sign in to access the student management system.</p>
    <?php if ($message = flash('error')): ?><div class="toast error"><?php echo e($message); ?></div><?php endif; ?>
    <?php if ($message = flash('success')): ?><div class="toast success"><?php echo e($message); ?></div><?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <label>Email<input type="email" name="email" required></label>
        <label>Password<input type="password" name="password" required></label>
        <button type="submit">Sign in</button>
    </form>
    <p>Don't have an account? <a href="/register.php">Register here</a></p>
</section>
</body>
</html>
