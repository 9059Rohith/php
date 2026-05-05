<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_login();

$metrics = student_metrics();
$recent = pdo()->query('SELECT al.timestamp, al.action, al.entity, al.entity_id, u.name AS actor FROM activity_log al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.timestamp DESC LIMIT 10')->fetchAll();

require __DIR__ . '/includes/header.php';
$currentUser = current_user();
?>
<div class="dashboard-welcome">
    <div class="welcome-banner">
        <div>
            <h1>Welcome, <?php echo e($currentUser['name']); ?>! 👋</h1>
            <p>You have <?php echo e(count($recent)); ?> recent activities. Great job staying on top of things!</p>
        </div>
        <div class="welcome-icon">📋</div>
    </div>
</div>

<section class="dashboard-grid">
    <article class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-content">
            <span>Total Students</span>
            <strong><?php echo e($metrics['students']); ?></strong>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-content">
            <span>Active Students</span>
            <strong><?php echo e($metrics['active_students']); ?></strong>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-icon">🏢</div>
        <div class="stat-content">
            <span>Departments</span>
            <strong><?php echo e($metrics['departments']); ?></strong>
        </div>
    </article>
    <article class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-content">
            <span>Pending Fees</span>
            <strong><?php echo e($metrics['pending_fees']); ?></strong>
        </div>
    </article>
</section>

<div class="dashboard-bottom">
    <section class="panel activity-panel">
        <div class="panel-header">
            <h2>📋 Recent Activity</h2>
            <span class="activity-count"><?php echo count($recent); ?> activities</span>
        </div>
        <div class="activity-list">
        <?php foreach ($recent as $row): ?>
            <div class="activity-item">
                <div class="activity-icon">🔔</div>
                <div class="activity-content">
                    <p class="activity-action"><strong><?php echo e($row['actor'] ?? 'System'); ?></strong> <?php echo e($row['action']); ?> <span class="entity-name"><?php echo e($row['entity']); ?> #<?php echo e($row['entity_id']); ?></span></p>
                    <p class="activity-time">⏰ <?php echo e($row['timestamp']); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    </section>

    <section class="panel quick-links-panel">
        <h2>🚀 Quick Links</h2>
        <div class="quick-links">
            <a href="/modules/students/add.php" class="quick-link">
                <span class="link-icon">➕</span>
                <span>Add Student</span>
            </a>
            <a href="/modules/students/index.php" class="quick-link">
                <span class="link-icon">👤</span>
                <span>View Students</span>
            </a>
            <a href="/modules/attendance/index.php" class="quick-link">
                <span class="link-icon">✓</span>
                <span>Attendance</span>
            </a>
            <a href="/modules/marks/index.php" class="quick-link">
                <span class="link-icon">📊</span>
                <span>Marks</span>
            </a>
            <a href="/modules/fees/index.php" class="quick-link">
                <span class="link-icon">💳</span>
                <span>Fees</span>
            </a>
            <a href="/modules/reports/index.php" class="quick-link">
                <span class="link-icon">📈</span>
                <span>Reports</span>
            </a>
        </div>
    </section>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
