<!DOCTYPE html>
<html>

<link rel="stylesheet" href="<?= FULL_URL ?>/public/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
.authnavbar-center {
  display: flex;
  justify-content: center;
  align-items: center;
}

.authnavbar-center h1 {
  margin: 0;
  font-size: 28px;
  letter-spacing: 6px;
  font-weight: 700;
}

.authnavbar-center a {
  color: #771ae8;
  text-decoration: none;
  transition: color 0.3s ease;
}

.authnavbar-center a:hover {
  color: #e8a71aff;
}
</style>

<body>

<nav class="navbar">
  <div class="authnavbar-center">
    <h1><a href="<?= FULL_URL ?>/">E V E N T Z.com</a></h1>
  </div>
</nav>

<div class="auth-container">

    <div class="auth-card">
        <h2>Login to EVENTZ</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']) ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form action="<?= FULL_URL ?>/login" method="POST" class="auth-form" data-ajax-form>
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

</body>
</html>