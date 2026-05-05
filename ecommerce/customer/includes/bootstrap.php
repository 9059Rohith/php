<?php
declare(strict_types=1);

require_once dirname(__DIR__, 3) . '/shared/core.php';

$GLOBALS['APP_CONFIG'] = [
    'name' => 'E-Commerce Platform',
    'root' => dirname(__DIR__, 2),
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

require_once dirname(__DIR__, 2) . '/includes/auth.php';
require_once dirname(__DIR__, 2) . '/includes/cart.php';
require_once dirname(__DIR__, 2) . '/includes/functions.php';
