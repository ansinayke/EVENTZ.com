<?php 
$pageTitle = 'Home - Sponsor';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Welcome, <?= $_SESSION['user_name'] ?>!</h1>
            <p>Discover events to sponsor</p>
        </div>
        
        <section class="events-section">
            <h2>Available Events</h2>
            <?php if (!empty($events)): ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
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
                        <button class="btn btn-primary">Sponsor This Event</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No events available at the moment.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>