<?php 
$pageTitle = 'Home - Participant';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Welcome, <?= $_SESSION['user_name'] ?>!</h1>
            <p>Discover events tailored to your interests</p>
        </div>
        
        <?php if (!empty($ongoingEvents)): ?>
        <section class="lives-section mb-3">
            <h2>Live Events</h2>
            <div class="lives-scroll">
                <?php foreach ($ongoingEvents as $event): ?>
                <div class="live-card">
                    <div class="live-badge">LIVE</div>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?? 'images/default-event.jpg' ?>" alt="<?= htmlspecialchars($event['title']) ?>">
                    <p><?= htmlspecialchars($event['title']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        
        <section class="recommended-events">
            <h2>Recommended Events</h2>
            <?php if (!empty($recommendedEvents)): ?>
            <div class="events-grid">
                <?php foreach ($recommendedEvents as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location_text']) ?></div>
                            <div><i class="fas fa-user"></i> <?= htmlspecialchars($event['organizer_name']) ?></div>
                            <div><i class="fas fa-users"></i> <?= $event['registration_count'] ?> registered</div>
                        </div>
                        <div class="event-actions">
                            <button onclick="registerForEvent(<?= $event['id'] ?>)" class="btn btn-primary">Register</button>
                            <a href="<?= FULL_URL ?>/participant/event-details?id=<?= $event['id'] ?>" class="btn btn-outline">Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No recommended events at the moment. Try following some organizers!</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>