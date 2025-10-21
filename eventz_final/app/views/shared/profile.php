<?php 
$pageTitle = 'Edit Profile';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Edit Profile</h1>
        </div>
        
        <div class="profile-edit-container">
            <form action="<?= FULL_URL ?>/profile/update" method="POST" enctype="multipart/form-data" data-ajax-form>
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="email">Email (Cannot be changed)</label>
                    <input type="email" value="<?= htmlspecialchars($user['email']) ?>" disabled class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" class="form-control" rows="4"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="website">Website</label>
                    <input type="url" id="website" name="website" value="<?= htmlspecialchars($user['website'] ?? '') ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" value="<?= htmlspecialchars($user['location'] ?? '') ?>" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="avatar">Profile Picture</label>
                    <?php if ($user['avatar_url']): ?>
                    <div class="current-avatar">
                        <img src="<?= FULL_URL ?>/public/<?= $user['avatar_url'] ?>" alt="Current Avatar">
                    </div>
                    <?php endif; ?>
                    <input type="file" id="avatar" name="avatar" accept="image/*" class="form-control">
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
            </form>
        </div>
    </div>
</div>

<style>
.profile-edit-container {
    max-width: 600px;
    margin: 0 auto;
    background: white;
    padding: 2rem;
    border-radius: 8px;
}
.current-avatar {
    margin-bottom: 1rem;
}
.current-avatar img {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    object-fit: cover;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>