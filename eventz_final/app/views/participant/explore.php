<?php 
$pageTitle = 'Explore Events';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Explore Events</h1>
        </div>
        
        <div class="filters-section mb-3">
            <form action="<?= FULL_URL ?>/participant/explore" method="GET" class="filters-form">
                <div class="form-row">
                    <input type="text" name="search" placeholder="Search events..." class="form-control" value="<?= htmlspecialchars($search ?? '') ?>">
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= ($selectedCategory == $category['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
        <!--
        <?php if (!empty($topParticipants)): ?>
        <section class="recommendations mb-3">
            <h3>Top Participants</h3>
            <div class="participants-scroll">
                <?php foreach ($topParticipants as $participant): ?>
                <div class="participant-card">
                    <?php if ($participant['avatar_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $participant['avatar_url'] ?>" alt="<?= htmlspecialchars($participant['full_name']) ?>">
                    <?php else: ?>
                    <i class="fas fa-user-circle"></i>
                    <?php endif; ?>
                    <p><?= htmlspecialchars($participant['full_name']) ?></p>
                    <small><?= $participant['participation_count'] ?> events</small>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        -->
        <section class="events-section">
            <h2>All Events</h2>
            <?php if (!empty($events)): ?>
            <div class="events-grid">
                <?php foreach ($events as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <?php 
                        $categoryNames = explode(',', $event['category_names'] ?? '');
                        $categoryColors = explode(',', $event['category_colors'] ?? '');
                        $firstCategory = trim($categoryNames[0] ?? '');
                        $firstColor = trim($categoryColors[0] ?? '#6366f1');
                        ?>
                        <span class="badge" style="background-color: <?= $firstColor ?>">
                            <?= htmlspecialchars($firstCategory) ?>
                        </span>
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($event['location_text'] ?? $event['location'] ?? 'Location TBD') ?></div>
                            <div><i class="fas fa-users"></i> <?= $event['registration_count'] ?? 0 ?> registered</div>
                        </div>
                        <div class="event-actions">
                            <button onclick="registerForEvent(<?= $event['id'] ?>)" class="btn btn-primary">Register</button>
                            <button onclick="showEventDetails(<?= $event['id'] ?>)" class="btn btn-outline">View Details</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No events found. Try adjusting your search criteria.</p>
            <?php endif; ?>
        </section>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>