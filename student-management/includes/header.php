<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';
$user = current_user();
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(app_name()); ?></title>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">
            <span class="brand-mark">📚</span>
            <div>
                <strong><?php echo e(app_name()); ?></strong>
                <small>Student Portal</small>
            </div>
        </div>
        <nav>
            <a href="/index.php" class="nav-item active"><span class="icon">📊</span> Dashboard</a>
            <a href="/modules/students/index.php" class="nav-item"><span class="icon">👥</span> Students</a>
            <a href="/modules/departments/index.php" class="nav-item"><span class="icon">🏢</span> Departments</a>
            <a href="/modules/courses/index.php" class="nav-item"><span class="icon">📖</span> Courses</a>
            <a href="/modules/marks/index.php" class="nav-item"><span class="icon">📝</span> Marks</a>
            <a href="/modules/attendance/index.php" class="nav-item"><span class="icon">✅</span> Attendance</a>
            <a href="/modules/fees/index.php" class="nav-item"><span class="icon">💰</span> Fees</a>
            <a href="/modules/reports/index.php" class="nav-item"><span class="icon">📈</span> Reports</a>
        </nav>
        <div class="sidebar-footer">
            <a href="/logout.php" class="logout-btn"><span class="icon">🚪</span> Logout</a>
        </div>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <div>
                <h1><?php echo e(app_name()); ?></h1>
                <p>Manage students, marks, attendance, and fees.</p>
            </div>
            <div class="topbar-user">
                <?php if ($user): ?>
                    <span><?php echo e($user['name']); ?> · <?php echo e($user['role']); ?></span>
                    <a href="/logout.php">Logout</a>
                <?php else: ?>
                    <a href="/login.php">Login</a>
                <?php endif; ?>
            </div>
        </header>
        <?php if ($message = flash('success')): ?>
            <div class="toast success"><?php echo e($message); ?></div>
        <?php endif; ?>
        <?php if ($message = flash('error')): ?>
            <div class="toast error"><?php echo e($message); ?></div>
        <?php endif; ?>
