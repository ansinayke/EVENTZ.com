<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVENTZ - Event Management Platform</title>
    <link rel="stylesheet" href="<?= FULL_URL ?>/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="welcome-page">
    <div class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Welcome to EVENTZ</h1>
                <p class="hero-subtitle">Discover, Create, and Manage Amazing Events</p>
                <div class="hero-buttons">
                    <a href="<?= FULL_URL ?>/register" class="btn btn-primary btn-lg">Get Started</a>
                    <a href="<?= FULL_URL ?>/login" class="btn btn-outline btn-lg">Login</a>
                </div>
            </div>
        </div>
    </div>
    
    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose EVENTZ?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>Easy Event Management</h3>
                    <p>Create and manage events with our intuitive platform</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Connect with People</h3>
                    <p>Build your network and follow your favorite organizers</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Track Analytics</h3>
                    <p>Get insights into your events and audience</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Sponsorship Opportunities</h3>
                    <p>Connect sponsors with amazing events</p>
                </div>
            </div>
        </div>
    </section>
    
    <section class="cta-section">
        <div class="container">
            <h2>Ready to Get Started?</h2>
            <p>Join thousands of event organizers and participants</p>
            <a href="<?= FULL_URL ?>/register" class="btn btn-primary btn-lg">Create Your Account</a>
        </div>
    </section>

    <?php require_once __DIR__ . '/layouts/footer.php'; ?>

</body>
</html>