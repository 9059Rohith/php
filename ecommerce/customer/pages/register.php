<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$error = null;

if (request_method() === 'POST') {
    verify_csrf();
    
    $name = clean_text(request('name', ''));
    $email = clean_text(request('email', ''));
    $phone = clean_text(request('phone', ''));
    $password = (string) request('password', '');
    
    // Validate inputs
    if (!$name) {
        $error = 'Name is required.';
    } elseif (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Valid email is required.';
    } elseif (!$password || strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!$phone) {
        $error = 'Phone number is required.';
    }
    
    if (!$error) {
        try {
            $stmt = pdo()->prepare('INSERT INTO users (name, email, password_hash, phone, role, email_verified, created_at) VALUES (:name, :email, :password_hash, :phone, "customer", 1, NOW())');
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ':phone' => $phone,
            ]);
            flash('success', 'Account created successfully! Please login.');
            redirect('/customer/pages/login.php');
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                $error = 'Email already registered. Please login or use a different email.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}

require __DIR__ . '/../includes/header.php';
?>
<div class="auth-container">
    <h2>Create Account</h2>
    <?php if ($error): ?>
        <div class="error-message"><?php echo e($error); ?></div>
    <?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <input type="text" name="name" placeholder="Full Name" value="<?php echo e(request('name', '')); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo e(request('email', '')); ?>" required>
        <input type="tel" name="phone" placeholder="Phone" value="<?php echo e(request('phone', '')); ?>" required>
        <input type="password" name="password" placeholder="Password (min 6 chars)" required>
        <button type="submit">Create Account</button>
    </form>
    <p>Already have an account? <a href="/customer/pages/login.php">Login here</a></p>
</div>
<?php require __DIR__ . '/../includes/footer.php';
