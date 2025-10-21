<?php 
$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Admin Dashboard</h1>
        </div>
        
        <section class="events-section mb-3">
            <h2>Pending Events (Awaiting Approval)</h2>
            <?php if (!empty($pendingEvents)): ?>
            <div class="events-grid">
                <?php foreach ($pendingEvents as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <span class="badge badge-warning">Pending</span>
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-user"></i> <?= htmlspecialchars($event['organizer_name']) ?></div>
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location_text']) ?></div>
                        </div>
                        <p><?= htmlspecialchars(substr($event['description'], 0, 100)) ?>...</p>
                        <div class="event-actions">
                            <button onclick="approveEvent(<?= $event['id'] ?>)" class="btn btn-success">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button onclick="rejectEvent(<?= $event['id'] ?>)" class="btn btn-danger">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No pending events.</p>
            <?php endif; ?>
        </section>
        
        <section class="events-section">
            <h2>Approved Events</h2>
            <?php if (!empty($approvedEvents)): ?>
            <div class="events-grid">
                <?php foreach (array_slice($approvedEvents, 0, 6) as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <span class="badge badge-success">Approved</span>
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-user"></i> <?= htmlspecialchars($event['organizer_name']) ?></div>
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-users"></i> <?= $event['registration_count'] ?> registered</div>
                        </div>
                        <div class="event-actions">
                            <button onclick="deleteEvent(<?= $event['id'] ?>)" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="text-center mt-2">Showing <?= min(6, count($approvedEvents)) ?> of <?= count($approvedEvents) ?> approved events</p>
            <?php else: ?>
            <p>No approved events yet.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>