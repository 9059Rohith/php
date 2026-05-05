<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

$error = flash('error');

if (request_method() === 'POST') {
    verify_csrf();
    if (ecommerce_login(clean_text(request('email', '')), (string) request('password', ''))) {
        redirect('/customer/pages/account.php');
    }
    $error = 'Invalid email or password.';
}

require __DIR__ . '/../includes/header.php';
?>
<div class="auth-container">
    <h2>Login</h2>
    <?php if ($error): ?>
        <div class="error-message"><?php echo e($error); ?></div>
    <?php endif; ?>
    <form method="post">
        <?php echo csrf_field(); ?>
        <input type="email" name="email" placeholder="Email" value="<?php echo e(request('email', '')); ?>" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="/customer/pages/register.php">Register here</a></p>
</div>
<?php require __DIR__ . '/../includes/footer.php';
