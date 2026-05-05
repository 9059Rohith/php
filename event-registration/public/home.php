<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require __DIR__ . '/../includes/header.php';
$events = pdo()->query('SELECT e.*, c.name AS category_name FROM events e LEFT JOIN event_categories c ON c.id = e.category_id WHERE e.status = "published" ORDER BY e.featured DESC, e.event_date ASC LIMIT 6')->fetchAll();
?>
<section class="hero">
    <h1>Discover, register, and check in with ease.</h1>
    <p>Core PHP event registration with QR confirmations, waitlists, and role-based dashboards.</p>
    <a class="cta" href="listing.php">Browse events</a>
</section>
<section class="cards-grid">
    <?php foreach ($events as $event): ?>
        <article class="event-card">
            <h3><?php echo e($event['title']); ?></h3>
            <p><?php echo e($event['city']); ?> · <?php echo e($event['event_date']); ?></p>
            <a href="event-detail.php?slug=<?php echo e($event['slug']); ?>">Open</a>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
