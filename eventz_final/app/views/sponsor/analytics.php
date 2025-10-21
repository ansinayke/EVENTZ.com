<?php 
$pageTitle = 'Sponsor Analytics';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Sponsorship Analytics</h1>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Sponsorships</h3>
                <div class="stat-value"><?= $stats['total_sponsorships'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
                <h3>Active Sponsorships</h3>
                <div class="stat-value"><?= $stats['active_sponsorships'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
                <h3>Completed Sponsorships</h3>
                <div class="stat-value"><?= $stats['completed_sponsorships'] ?? 0 ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Amount</h3>
                <div class="stat-value">$<?= number_format($stats['total_amount'] ?? 0, 2) ?></div>
            </div>
        </div>
        
        <?php if (!empty($sponsorships)): ?>
        <section class="sponsorships-history mt-3">
            <h2>Sponsorship History</h2>
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