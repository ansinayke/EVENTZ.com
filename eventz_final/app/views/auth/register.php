<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Register for EVENTZ</h2>
        <form action="<?= FULL_URL ?>/register" method="POST" class="auth-form">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" required class="form-control" 
                       value="<?= $_SESSION['old_input']['full_name'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required class="form-control"
                       value="<?= $_SESSION['old_input']['email'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" class="form-control"
                       value="<?= $_SESSION['old_input']['phone'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" id="location" name="location" class="form-control"
                       value="<?= $_SESSION['old_input']['location'] ?? '' ?>">
            </div>
            
            <div class="form-group">
                <label for="role">I am a...</label>
                <select id="role" name="role" required class="form-control">
                    <option value="">Select Role</option>
                    <option value="participant" <?= ($_SESSION['old_input']['role'] ?? '') === 'participant' ? 'selected' : '' ?>>Participant</option>
                    <option value="organizer" <?= ($_SESSION['old_input']['role'] ?? '') === 'organizer' ? 'selected' : '' ?>>Organizer</option>
                    <option value="sponsor" <?= ($_SESSION['old_input']['role'] ?? '') === 'sponsor' ? 'selected' : '' ?>>Sponsor</option>
                    <option value="supplier" <?= ($_SESSION['old_input']['role'] ?? '') === 'supplier' ? 'selected' : '' ?>>Supplier</option>
                </select>
            </div>
            
            <div class="form-group">
                <label>Interests (Select categories you're interested in)</label>
                <div class="checkbox-grid">
                    <?php foreach ($categories as $category): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="interests[]" value="<?= $category['id'] ?>">
                        <span><?= htmlspecialchars($category['name']) ?></span>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required class="form-control" minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control" minlength="6">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Register</button>
        </form>
        
        <p class="auth-footer">
            Already have an account? <a href="<?= FULL_URL ?>/login">Login here</a>
        </p>
    </div>
</div>

<?php 
unset($_SESSION['old_input']);
require_once __DIR__ . '/../layouts/footer.php'; 
?>