<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Login to EVENTZ</h2>
        <form action="<?= FULL_URL ?>/login" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <p class="auth-footer">
            Don't have an account? <a href="<?= FULL_URL ?>/register">Register here</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>