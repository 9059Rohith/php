<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
if (request_method() === 'POST') {
    verify_csrf();
    if (event_login(clean_text(request('email', '')), (string) request('password', ''))) {
        redirect('/event-registration/admin/dashboard.php');
    }
}
?><!doctype html><html><body><form method="post"><?php echo csrf_field(); ?><input type="email" name="email"><input type="password" name="password"><button>Login</button></form></body></html>
