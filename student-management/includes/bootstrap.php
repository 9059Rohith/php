<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/shared/core.php';

$GLOBALS['APP_CONFIG'] = [
    'name' => 'Student Management System',
    'root' => dirname(__DIR__),
    'assets_url' => '',
    'db' => [
        'driver' => env_value('STUDENT_DB_DRIVER', 'mysql'),
        'host' => env_value('STUDENT_DB_HOST', '127.0.0.1'),
        'port' => env_value('STUDENT_DB_PORT', '3306'),
        'database' => env_value('STUDENT_DB_NAME', 'student_db'),
        'username' => env_value('STUDENT_DB_USER', 'root'),
        'password' => env_value('STUDENT_DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
];


function current_user(): ?array
{
    static $cached = null;

    if ($cached !== null) {
        return $cached;
    }

    $userId = $_SESSION['user_id'] ?? null;

    if (!$userId) {
        return $cached = null;
    }

    $stmt = pdo()->prepare('SELECT id, name, email, role FROM users WHERE id = :id LIMIT 1');
    $stmt->execute([':id' => $userId]);

    return $cached = $stmt->fetch() ?: null;
}

function require_login(): void
{
    if (!current_user()) {
        redirect('/login.php');
    }
}

function require_role(array $roles): void
{
    $user = current_user();

    if (!$user || !in_array($user['role'], $roles, true)) {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}
