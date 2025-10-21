<?php 
$pageTitle = 'Organizer Dashboard';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Organizer Dashboard</h1>
            <button class="btn btn-primary" data-modal="createEventModal">
                <i class="fas fa-plus"></i> Create Event
            </button>
        </div>
        
        <section class="events-section mb-3">
            <h2>Upcoming Events</h2>
            <?php if (!empty($upcomingEvents)): ?>
            <div class="events-grid">
                <?php foreach ($upcomingEvents as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <?php 
                        $statusClass = '';
                        $statusText = '';
                        switch($event['status']) {
                            case 'approved':
                                $statusClass = 'badge-success';
                                $statusText = 'Approved';
                                break;
                            case 'pending':
                                $statusClass = 'badge-warning';
                                $statusText = 'Pending';
                                break;
                            case 'rejected':
                                $statusClass = 'badge-danger';
                                $statusText = 'Rejected';
                                break;
                            default:
                                $statusClass = 'badge-secondary';
                                $statusText = ucfirst($event['status']);
                        }
                        ?>
                        <span class="badge <?= $statusClass ?>">
                            <?= $statusText ?>
                        </span>
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-users"></i> <?= $event['registration_count'] ?? 0 ?> registered</div>
                        </div>
                        <div class="event-actions">
                            <button onclick="editEvent(<?= $event['id'] ?>)" class="btn btn-outline">Edit</button>
                            <button onclick="deleteEvent(<?= $event['id'] ?>)" class="btn btn-danger">Delete</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p>No upcoming events. Create your first event!</p>
            <?php endif; ?>
        </section>
        
        <?php if (!empty($ongoingEvents)): ?>
        <section class="events-section mb-3">
            <h2>Ongoing Events</h2>
            <div class="events-grid">
                <?php foreach ($ongoingEvents as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <span class="badge badge-success">Ongoing</span>
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-users"></i> <?= $event['registration_count'] ?? 0 ?> participants</div>
                        </div>
                        <div class="event-actions">
                            <button data-upload-video="<?= $event['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-video"></i> Upload Video
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        
        <?php if (!empty($completedEvents)): ?>
        <section class="events-section">
            <h2>Completed Events</h2>
            <div class="events-grid">
                <?php foreach ($completedEvents as $event): ?>
                <div class="event-card">
                    <?php if ($event['banner_url']): ?>
                    <img src="<?= FULL_URL ?>/public/<?= $event['banner_url'] ?>" alt="<?= htmlspecialchars($event['title']) ?>" class="event-banner">
                    <?php endif; ?>
                    <div class="event-content">
                        <span class="badge badge-secondary">Completed</span>
                        <h3 class="event-title"><?= htmlspecialchars($event['title']) ?></h3>
                        <div class="event-meta">
                            <div><i class="fas fa-calendar"></i> <?= date('M d, Y', strtotime($event['start_at'])) ?></div>
                            <div><i class="fas fa-users"></i> <?= $event['registration_count'] ?? 0 ?> participants</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>
</div>

<!-- Create Event Modal -->
<div id="createEventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Create New Event</h2>
            <button class="modal-close">&times;</button>
        </div>
        <form action="<?= FULL_URL ?>/organizer/create-event" method="POST" enctype="multipart/form-data" data-ajax-form>
            <div class="form-group">
                <label for="title">Event Title</label>
                <input type="text" id="title" name="title" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required class="form-control" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required class="form-control">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="event_date">Event Date</label>
                <input type="date" id="event_date" name="event_date" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="event_time">Event Time</label>
                <input type="time" id="event_time" name="event_time" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="venue">Venue</label>
                <input type="text" id="venue" name="venue" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="capacity">Capacity (Optional)</label>
                <input type="number" id="capacity" name="capacity" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="banner">Event Banner</label>
                <input type="file" id="banner" name="banner" accept="image/*" class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Create Event</button>
        </form>
    </div>
</div>

<!-- Edit Event Modal -->
<div id="editEventModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Edit Event</h2>
            <button class="modal-close">&times;</button>
        </div>
        <form action="<?= FULL_URL ?>/organizer/update-event" method="POST" enctype="multipart/form-data" data-ajax-form>
            <input type="hidden" id="edit_event_id" name="event_id">
            
            <div class="form-group">
                <label for="edit_title">Event Title</label>
                <input type="text" id="edit_title" name="title" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_description">Description</label>
                <textarea id="edit_description" name="description" required class="form-control" rows="4"></textarea>
            </div>
            
            <div class="form-group">
                <label for="edit_category_id">Category</label>
                <select id="edit_category_id" name="category_id" required class="form-control">
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit_event_date">Event Date</label>
                <input type="date" id="edit_event_date" name="event_date" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_event_time">Event Time</label>
                <input type="time" id="edit_event_time" name="event_time" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_location">Location</label>
                <input type="text" id="edit_location" name="location" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_venue">Venue</label>
                <input type="text" id="edit_venue" name="venue" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_capacity">Capacity (Optional)</label>
                <input type="number" id="edit_capacity" name="capacity" class="form-control">
            </div>
            
            <div class="form-group">
                <label for="edit_banner">Event Banner</label>
                <input type="file" id="edit_banner" name="banner" accept="image/*" class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Update Event</button>
        </form>
    </div>
</div>

<style>
.badge-warning {
    background: var(--warning-color);
    color: white;
}

.badge-success {
    background: var(--success-color);
    color: white;
}

.badge-danger {
    background: var(--danger-color);
    color: white;
}

.badge-secondary {
    background: #6b7280;
    color: white;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>