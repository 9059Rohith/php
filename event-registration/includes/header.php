<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
$user = event_current_user();
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(app_name()); ?></title>
    <link rel="stylesheet" href="/event-registration/assets/app.css">
</head>
<body>
<header class="site-topbar">
    <a href="/event-registration/public/home.php" class="brand">EventFlow</a>
    <nav>
        <a href="/event-registration/public/listing.php">Events</a>
        <a href="/event-registration/participant/dashboard.php">My Dashboard</a>
        <a href="/event-registration/organizer/dashboard.php">Organizer</a>
        <a href="/event-registration/admin/dashboard.php">Admin</a>
        <?php if ($user): ?><span><?php echo e($user['name']); ?></span><?php endif; ?>
    </nav>
</header>
<main class="site-body">
