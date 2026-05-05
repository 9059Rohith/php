<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
$event = event_by_slug(clean_text(request('slug', '')));
if (!$event) {
    http_response_code(404);
    echo 'Event not found';
    exit;
}
$tickets = event_ticket_types((int) $event['id']);
$sessions = pdo()->prepare('SELECT * FROM event_sessions WHERE event_id = :event_id ORDER BY start_time ASC');
$sessions->execute([':event_id' => $event['id']]);
require __DIR__ . '/../includes/header.php';
?>
<section class="event-detail">
    <h1><?php echo e($event['title']); ?></h1>
    <p><?php echo e($event['venue']); ?> · <?php echo e($event['city']); ?></p>
    <p><?php echo e($event['description']); ?></p>
    <h2>Tickets</h2>
    <?php foreach ($tickets as $ticket): ?><p><?php echo e($ticket['name']); ?> · <?php echo e($ticket['quantity_available'] - $ticket['quantity_sold']); ?> left</p><?php endforeach; ?>
    <h2>Sessions</h2>
    <?php foreach ($sessions as $session): ?><p><?php echo e($session['title']); ?> · <?php echo e($session['speaker_name']); ?></p><?php endforeach; ?>
    <a class="cta" href="register.php?event=<?php echo e($event['slug']); ?>">Register</a>
</section>
<?php require __DIR__ . '/../includes/footer.php'; ?>
