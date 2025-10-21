<?php 
$pageTitle = 'My Portfolio';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="profile-header">
            <div class="profile-info">
                <?php if ($user['avatar_url']): ?>
                <img src="<?= FULL_URL ?>/public/<?= $user['avatar_url'] ?>" alt="Avatar" class="profile-avatar">
                <?php else: ?>
                <i class="fas fa-user-circle profile-avatar-icon"></i>
                <?php endif; ?>
                <div>
                    <h1><?= htmlspecialchars($user['full_name']) ?></h1>
                    <p><?= htmlspecialchars($user['email']) ?></p>
                    <?php if ($user['bio']): ?>
                    <p class="bio"><?= htmlspecialchars($user['bio']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= FULL_URL ?>/profile" class="btn btn-outline">Edit Profile</a>
        </div>
        
        <div class="stats-grid">
            <!-- <div class="stat-card">
                <h3>Events Participated</h3>
                <div class="stat-value"><?= $stats['total_participated'] ?></div>
            </div> -->
            <div class="stat-card">
                <h3>Registered Events</h3>
                <div class="stat-value"><?= $stats['total_registered'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Followers</h3>
                <div class="stat-value"><?= $stats['followers'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Following</h3>
                <div class="stat-value"><?= $stats['following'] ?></div>
            </div>
        </div>
        
        <section class="portfolio-section">
            <h2>Participated Events</h2>
            <?php if (!empty($participatedEvents)): ?>
            <div class="events-grid">
                <?php foreach ($participatedEvents as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <span class="badge badge-success">Participated</span>
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-user"></i> <?= htmlspecialchars($event['organizer_name']) ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>You haven't participated in any events yet.</p>
            <?php endif; ?>
        </section>
        
        <?php if (!empty($registeredEvents)): ?>
        <section class="portfolio-section mt-3">
            <h2>Upcoming Registered Events</h2>
            <div class="events-grid">
                <?php foreach ($registeredEvents as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <span class="badge badge-primary">Registered</span>
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-user"></i> <?= htmlspecialchars($event['organizer_name']) ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<style>
.profile-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}
.profile-info {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}
.profile-avatar {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}
.profile-avatar-icon {
    font-size: 100px;
    color: var(--text-light);
}
.bio {
    margin-top: 0.5rem;
    color: var(--text-light);
}
.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}
.badge-success {
    background: var(--success-color);
    color: white;
}
.badge-primary {
    background: var(--primary-color);
    color: white;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>