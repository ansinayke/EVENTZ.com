<?php 
$pageTitle = 'Settings';
require_once __DIR__ . '/../layouts/header.php'; 
?>

<div class="container">
    <div class="dashboard">
        <div class="dashboard-header">
            <h1>Settings</h1>
        </div>
        
        <div class="settings-container">
            <div class="settings-section">
                <h2>Account Settings</h2>
                <p>Manage your account preferences and settings.</p>
                <a href="<?= FULL_URL ?>/profile" class="btn btn-outline">Edit Profile</a>
            </div>
            
            <div class="settings-section">
                <h2>Privacy</h2>
                <p>Control who can see your information and activities.</p>
            </div>
            
            <div class="settings-section">
                <h2>Notifications</h2>
                <p>Manage your notification preferences.</p>
            </div>
            
            <div class="settings-section">
                <h2>Theme</h2>
                <p>Choose your preferred theme.</p>
                <div class="theme-options">
                    <button class="btn btn-outline">Light</button>
                    <button class="btn btn-outline">Dark</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.settings-container {
    max-width: 800px;
    margin: 0 auto;
}
.settings-section {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}
.settings-section h2 {
    margin-bottom: 0.5rem;
}
.settings-section p {
    color: var(--text-light);
    margin-bottom: 1rem;
}
.theme-options {
    display: flex;
    gap: 1rem;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>