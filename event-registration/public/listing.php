<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$term = clean_text(request('q', ''));
$sql = 'SELECT e.*, c.name AS category_name FROM events e LEFT JOIN event_categories c ON c.id = e.category_id WHERE e.status = "published"';
$params = [];
if ($term !== '') {
    $sql .= ' AND (e.title LIKE :term OR e.city LIKE :term OR e.venue LIKE :term)';
    $params[':term'] = '%' . $term . '%';
}
$stmt = pdo()->prepare($sql . ' ORDER BY e.event_date ASC');
$stmt->execute($params);
$events = $stmt->fetchAll();
require __DIR__ . '/../includes/header.php';
?>
<section class="page-head"><h1>Events</h1></section>
<form method="get" class="filters"><input name="q" value="<?php echo e($term); ?>" placeholder="Search title, location, organizer"><button>Search</button></form>
<section class="cards-grid">
    <?php foreach ($events as $event): ?>
        <article class="event-card">
            <h3><?php echo e($event['title']); ?></h3>
            <p><?php echo e($event['city']); ?> · <?php echo e($event['event_date']); ?></p>
            <a href="event-detail.php?slug=<?php echo e($event['slug']); ?>">Details</a>
        </article>
    <?php endforeach; ?>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
