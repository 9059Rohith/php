<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
event_require_roles(['admin']);
$stats = [
    'events' => (int) pdo()->query('SELECT COUNT(*) AS total FROM events')->fetch()['total'],
    'users' => (int) pdo()->query('SELECT COUNT(*) AS total FROM users')->fetch()['total'],
    'registrations' => (int) pdo()->query('SELECT COUNT(*) AS total FROM registrations')->fetch()['total'],
    'revenue' => (float) pdo()->query('SELECT COALESCE(SUM(total_amount),0) AS total FROM registrations')->fetch()['total'],
];
$pendingApps = (int) (pdo()->query("SELECT COUNT(*) AS total FROM organizer_applications WHERE status = 'pending'")->fetch()['total'] ?? 0);
$recentEvents = pdo()->query('SELECT title, status, event_date FROM events ORDER BY created_at DESC LIMIT 5')->fetchAll();
?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/event-registration/assets/app.css">
    <title>Admin Dashboard</title>
</head>
<body>
<main class="panel">
    <h1>Platform Admin</h1>
    <section class="cards-grid">
        <article class="event-card"><h3>Events</h3><strong><?php echo e($stats['events']); ?></strong></article>
        <article class="event-card"><h3>Users</h3><strong><?php echo e($stats['users']); ?></strong></article>
        <article class="event-card"><h3>Registrations</h3><strong><?php echo e($stats['registrations']); ?></strong></article>
        <article class="event-card"><h3>Revenue</h3><strong><?php echo e(number_format($stats['revenue'], 2)); ?></strong></article>
    </section>
    <section class="panel" style="margin-top:16px;">
        <h2>Pending organizer applications</h2>
        <p><?php echo e($pendingApps); ?></p>
        <h2>Recent events</h2>
        <?php foreach ($recentEvents as $event): ?>
            <p><?php echo e($event['title']); ?> · <?php echo e($event['status']); ?> · <?php echo e($event['event_date']); ?></p>
        <?php endforeach; ?>
    </section>
</main>
</body>
</html>
