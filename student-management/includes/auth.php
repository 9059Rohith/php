<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

function attempt_login(string $email, string $password): bool
{
    $stmt = pdo()->prepare('SELECT id, password_hash, status, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || ($user['status'] ?? 'active') !== 'active') {
        return false;
    }

    if (!password_verify($password, $user['password_hash'])) {
        return false;
    }

    $_SESSION['user_id'] = (int) $user['id'];
    session_regenerate_id(true);

    return true;
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']);
    }

    session_destroy();
}

function password_hash_for_user(string $password): string
{
    return password_hash($password, PASSWORD_DEFAULT);
}
