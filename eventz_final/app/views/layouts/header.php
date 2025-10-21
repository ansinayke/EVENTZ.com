<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Security-Policy" content="default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com; img-src 'self' data: https:; connect-src 'self';">
    <title><?= $pageTitle ?? 'EVENTZ - Event Management Platform' ?></title>
    <link rel="stylesheet" href="<?= FULL_URL ?>/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="<?= FULL_URL ?>/">
                    <h1>EVENTZ</h1>
                </a>
            </div>
            
            <?php if (isset($_SESSION['user_id'])): ?>
            <div class="navbar-menu">
                <ul class="navbar-nav">
                    <?php
                    $role = $_SESSION['user_role'] ?? 'participant';
                    
                    switch($role) {
                        case 'participant':
                            echo '<li><a href="' . FULL_URL . '/participant/home"><i class="fas fa-home"></i> Home</a></li>';
                            echo '<li><a href="' . FULL_URL . '/participant/explore"><i class="fas fa-compass"></i> Explore</a></li>';
                            echo '<li><a href="' . FULL_URL . '/participant/portfolio"><i class="fas fa-briefcase"></i> Portfolio</a></li>';
                            break;
                        case 'organizer':
                            echo '<li><a href="' . FULL_URL . '/organizer/home"><i class="fas fa-home"></i> Home</a></li>';
                            echo '<li><a href="' . FULL_URL . '/organizer/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>';
                            echo '<li><a href="' . FULL_URL . '/organizer/analytics"><i class="fas fa-chart-line"></i> Analytics</a></li>';
                            break;
                        case 'sponsor':
                            echo '<li><a href="' . FULL_URL . '/sponsor/home"><i class="fas fa-home"></i> Home</a></li>';
                            echo '<li><a href="' . FULL_URL . '/sponsor/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>';
                            echo '<li><a href="' . FULL_URL . '/sponsor/analytics"><i class="fas fa-chart-line"></i> Analytics</a></li>';
                            break;
                        case 'admin':
                            echo '<li><a href="' . FULL_URL . '/admin/dashboard"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>';
                            echo '<li><a href="' . FULL_URL . '/admin/analytics"><i class="fas fa-chart-line"></i> Analytics</a></li>';
                            break;
                    }
                    ?>
                </ul>
                
                <div class="navbar-search">
                    <form action="<?= FULL_URL ?>/search" method="GET">
                        <input type="text" name="q" placeholder="Search..." class="search-input">
                        <button type="submit" class="search-btn"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                
                <div class="navbar-user">
                    <div class="user-dropdown">
                        <button class="user-btn">
                            <?php if (isset($_SESSION['user_avatar']) && $_SESSION['user_avatar']): ?>
                                <img src="<?= FULL_URL ?>/public/<?= $_SESSION['user_avatar'] ?>" alt="Avatar">
                            <?php else: ?>
                                <i class="fas fa-user-circle"></i>
                            <?php endif; ?>
                            <span><?= $_SESSION['user_name'] ?? 'User' ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="dropdown-menu">
                            <a href="<?= FULL_URL ?>/profile"><i class="fas fa-user"></i> Profile</a>
                            <a href="<?= FULL_URL ?>/settings"><i class="fas fa-cog"></i> Settings</a>
                            <hr>
                            <a href="<?= FULL_URL ?>/logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="navbar-menu">
                <a href="<?= FULL_URL ?>/login" class="btn btn-outline">Login</a>
                <a href="<?= FULL_URL ?>/register" class="btn btn-primary">Register</a>
            </div>
            <?php endif; ?>
        </div>
    </nav>
    
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success'] ?>
        <?php unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= $_SESSION['error'] ?>
        <?php unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['errors'])): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <?php unset($_SESSION['errors']); ?>
    </div>
    <?php endif; ?>
    
    <main class="main-content">