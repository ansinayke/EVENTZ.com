<?php 
$pageTitle = 'Analytics';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Analytics</h1>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Events</h3>
                <div class="stat-value"><?= $stats['total_events'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Upcoming Events</h3>
                <div class="stat-value"><?= $stats['upcoming_events'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Completed Events</h3>
                <div class="stat-value"><?= $stats['completed_events'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Participants</h3>
                <div class="stat-value"><?= $stats['total_participants'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Views</h3>
                <div class="stat-value"><?= $stats['total_views'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Followers</h3>
                <div class="stat-value"><?= $stats['followers'] ?></div>
            </div>
        </div>
        
        <?php if (!empty($eventsByCategory)): ?>
        <section class="chart-section mt-3">
            <h2>Events by Category</h2>
            <div class="category-stats">
                <?php foreach ($eventsByCategory as $category => $count): ?>
                <div class="category-stat-item">
                    <span class="category-name"><?= htmlspecialchars($category) ?></span>
                    <span class="category-count"><?= $count ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($recentEvents)): ?>
        <section class="recent-events mt-3">
            <h2>Recent Events</h2>
            <div class="events-list">
                <?php foreach ($recentEvents as $event): ?>
                <div class="event-list-item">
                    <h4><?= htmlspecialchars($event['title']) ?></h4>
                    <div class="event-stats-inline">
                        <span><i class="fas fa-users"></i> <?= $event['registration_count'] ?> participants</span>
                        <span><i class="fas fa-eye"></i> <?= $event['view_count'] ?? 0 ?> views</span>
                        <span><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<style>
.category-stats {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
}
.category-stat-item {
    display: flex;
    justify-content: space-between;
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--border-color);
}
.category-stat-item:last-child {
    border-bottom: none;
}
.category-count {
    font-weight: bold;
    color: var(--primary-color);
}
.events-list {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
}
.event-list-item {
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}
.event-list-item:last-child {
    border-bottom: none;
}
.event-stats-inline {
    display: flex;
    gap: 1.5rem;
    margin-top: 0.5rem;
    color: var(--text-light);
    font-size: 0.9rem;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>