<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/shared/core.php';

$GLOBALS['APP_CONFIG'] = [
    'name' => 'E-Commerce Platform',
    'root' => dirname(__DIR__),
    'assets_url' => '',
    'db' => [
        'driver' => env_value('ECOM_DB_DRIVER', 'mysql'),
        'host' => env_value('ECOM_DB_HOST', '127.0.0.1'),
        'port' => env_value('ECOM_DB_PORT', '3306'),
        'database' => env_value('ECOM_DB_NAME', 'ecommerce_db'),
        'username' => env_value('ECOM_DB_USER', 'root'),
        'password' => env_value('ECOM_DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
];


function ecommerce_current_user(): ?array
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

function ecommerce_require_login(): void
{
    if (!ecommerce_current_user()) {
        redirect('/customer/pages/home.php');
    }
}

function ecommerce_require_admin(): void
{
    $user = ecommerce_current_user();
    if (!$user || $user['role'] !== 'admin') {
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}
