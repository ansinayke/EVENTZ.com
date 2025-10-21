<?php 
$pageTitle = 'Home - Organizer';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Welcome, <?= $_SESSION['user_name'] ?>!</h1>
            <p>Stay updated with events from organizers you follow</p>
        </div>
        
        <section class="feed-section">
            <h2>Event Feed</h2>
            <?php if (!empty($feedEvents)): ?>
            <div class="events-grid">
                <?php foreach ($feedEvents as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location']) ?></div>
                            <div><i class="fas fa-users"></i> <?= $event['registration_count'] ?> registered</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No events in your feed. Try following some organizers!</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>