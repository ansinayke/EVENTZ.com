<?php 
$pageTitle = 'System Analytics';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>System Analytics</h1>
        </div>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Users</h3>
                <div class="stat-value"><?= $stats['total_users'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Organizers</h3>
                <div class="stat-value"><?= $stats['total_organizers'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Participants</h3>
                <div class="stat-value"><?= $stats['total_participants'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Sponsors</h3>
                <div class="stat-value"><?= $stats['total_sponsors'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Suppliers</h3>
                <div class="stat-value"><?= $stats['total_suppliers'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Events</h3>
                <div class="stat-value"><?= $stats['total_events'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Approved Events</h3>
                <div class="stat-value"><?= $stats['approved_events'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Pending Events</h3>
                <div class="stat-value"><?= $stats['pending_events'] ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Registrations</h3>
                <div class="stat-value"><?= $stats['total_registrations'] ?></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>