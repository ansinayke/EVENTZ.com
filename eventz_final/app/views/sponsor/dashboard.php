<?php 
$pageTitle = 'Sponsor Dashboard';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Sponsor Dashboard</h1>
            <button class="btn btn-primary" data-modal="createPlanModal">
                <i class="fas fa-plus"></i> Create Sponsorship Plan
            </button>
        </div>
        
        <section class="plans-section mb-3">
            <h2>My Sponsorship Plans</h2>
            <?php if (!empty($plans)): ?>
            <div class="plans-grid">
                <?php foreach ($plans as $plan): ?>
                <div class="plan-card">
                    <h3><?= htmlspecialchars($plan['plan_name']) ?></h3>
                    <div class="plan-amount">$<?= number_format($plan['amount'], 2) ?></div>
                    <p><?= htmlspecialchars($plan['description']) ?></p>
                    <div class="plan-actions">
                        <button class="btn btn-outline">Edit</button>
                        <button class="btn btn-danger">Delete</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No sponsorship plans yet. Create your first plan!</p>
            <?php endif; ?>
        </section>
        
        <section class="events-section mb-3">
            <h2>Available Events to Sponsor</h2>
            <?php if (!empty($events)): ?>
            <div class="events-grid">
                <?php foreach (array_slice($events, 0, 6) as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-users"></i> <?= $event['registration_count'] ?> registered</div>
                        </div>
                        <button class="btn btn-primary">Sponsor This Event</button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </section>
        
        <?php if (!empty($sponsorships)): ?>
        <section class="sponsorships-section">
            <h2>My Sponsorships</h2>
            <div class="sponsorships-list">
                <?php foreach ($sponsorships as $sponsorship): ?>
                <div class="sponsorship-item">
                    <h4><?= htmlspecialchars($sponsorship['event_title']) ?></h4>
                    <div class="sponsorship-details">
                        <span>Amount: $<?= number_format($sponsorship['amount'], 2) ?></span>
                        <span>Status: <?= htmlspecialchars($sponsorship['status']) ?></span>
                        <span>Date: <?= date('M d, Y', strtotime($sponsorship['event_date'])) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<style>
.plans-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}
.plan-card {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
.plan-amount {
    font-size: 2rem;
    font-weight: bold;
    color: var(--primary-color);
    margin: 1rem 0;
}
.plan-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 1rem;
}
.sponsorships-list {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
}
.sponsorship-item {
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}
.sponsorship-item:last-child {
    border-bottom: none;
}
.sponsorship-details {
    display: flex;
    gap: 1.5rem;
    margin-top: 0.5rem;
    color: var(--text-light);
    font-size: 0.9rem;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>