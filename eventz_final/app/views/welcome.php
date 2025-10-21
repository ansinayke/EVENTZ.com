<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E V E N T Z.com</title>
  <link rel="stylesheet" href="<?= FULL_URL ?>/public/css/welcome.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    footer {
      background-color: #1f2732;
      color: #fff;
      padding: 60px 0 20px 0;
      font-size: 15px;
      margin-top: 100px;
    }

    .footer-container {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      max-width: 1200px;
      margin: auto;
      padding: 0 20px;
    }

    .footer-section {
      flex: 1;
      min-width: 250px;
      margin: 15px 0;
      color: #cfd8e3;
    }

    .footer-section h3 {
      font-family: 'Poppins', sans-serif;
      font-size: 20px;
      margin-bottom: 20px;
      color: #fff;
    }

    .footer-section p {
      font-family: 'Poppins', sans-serif;
      line-height: 1.6;
    }

    .footer-section ul {
      font-family: 'Poppins', sans-serif;
      list-style: none;
      padding: 0;
    }

    .footer-section ul li {
      margin-bottom: 10px;
    }

    .footer-section ul li a {
      color: #cfd8e3;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .footer-section ul li a:hover {
      color: #fff;
    }

    /* Social Media Icons */
    .social-links a {
      font-family: 'Poppins', sans-serif;
      display: inline-block;
      color: #cfd8e3;
      font-size: 20px;
      margin-right: 12px;
      transition: color 0.3s ease, transform 0.3s ease;
    }

    .social-links a:hover {
      color: #fff;
      transform: translateY(-3px);
    }

    /* Footer bottom */
    .footer-bottom {
      font-family: 'Poppins', sans-serif;
      text-align: center;
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #2e3745;
      color: #cfd8e3;
      font-size: 14px;
    }
  </style>
</head>

<body>
<div class="welcome-container">
  <section class="hero-section">
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="bg-shape shape-3"></div>
    <div class="bg-shape shape-4"></div>
    <div class="bg-shape shape-5"></div>

    <div class="hero-content">
      <h1 class="hero-title">E V E N T Z</h1>
      <p class="hero-subtitle2">The <b>P A S T ・ P R E S E N T ・ F U T U R E</b> of Events</p>
      <br>
      <p class="hero-description">
        Discover - Connect - Create - & Sponsor <br> events like never before. Connect with people who share your interests,
        showcase your events as an organizer as well as a participant, or sponsor meaningful experiences. All In One Unified platform.
      </p>
    </div>
    <br><br><br><br><br><br><br><br>
    <div class="cta-buttons">
      <a href="<?= FULL_URL ?>/register" class="btn-primary">Get Started</a>
      <a href="<?= FULL_URL ?>/login" class="btn-primary">Login</a>
    </div>
  </section>

  <section class="features-section">
    <h2 class="section-heading">Why Choose EVENTZ.com?</h2>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">🔎</div>
        <h3 class="feature-title">Discover Events</h3>
        <p class="feature-description">
          Find exciting events in your area based on your interests. From music festivals to tech conferences, 
          there's something for everyone.
        </p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">👥</div>
        <h3 class="feature-title">Connect</h3>
        <p class="feature-description">
          Meet like-minded people who share your passions, make portfolios. Build your network and create lasting memories.
        </p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">📅</div>
        <h3 class="feature-title">Organize</h3>
        <p class="feature-description">
          Create and manage your own events with our powerful tools, statistical insights and all. From small gatherings to large conferences.
        </p>
      </div>

      <div class="feature-card">
        <div class="feature-icon">💼</div>
        <h3 class="feature-title">Sponsor</h3>
        <p class="feature-description">
          Reach your target audience by sponsoring events that align with your brand values and business goals.
        </p>
      </div>
    </div>
  </section>

  <section class="stats-section">
    <div class="stats-grid">
      <div class="stat-item">
        <div class="stat-number"><?= $stats['total_events'] ?>+</div>
        <div class="stat-label">Events</div>
      </div>

      <div class="stat-item">
        <div class="stat-number"><?= number_format($stats['total_members']) ?></div>
        <div class="stat-label">Members</div>
      </div>

      <div class="stat-item">
        <div class="stat-number"><?= $stats['total_sponsors'] ?></div>
        <div class="stat-label">Sponsors</div>
      </div>

      <div class="stat-item">
        <div class="stat-number"><?= $stats['total_organizers'] ?></div>
        <div class="stat-label">Organizers</div>
      </div>
    </div>
  </section>

  <section class="cta-section">
    <h2 class="cta-title">Ready to Get Started?</h2>
    <p class="cta-subtitle">
      Join thousands of event enthusiasts, organizers, and sponsors who are already using EVENTZ 
      to create unforgettable experiences, and eventional image for yourself.
      <br><br><br>
    </p><br><br>
    <a href="<?= FULL_URL ?>/register" class="btn-primary">Create Your Account</a>
  </section>
</div>

<!-- Footer -->
<footer>
  <div class="footer-container">
    <div class="footer-section">
      <h3>EVENTZ.com</h3>
      <p>Your premier event management platform</p>
    </div>
    <div class="footer-section">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="/eventz_final">Home</a></li>
        <li><a href="/eventz_final/app/views/static/about.php">About</a></li>
        <li><a href="/eventz_final/app/views/static/contact.php">Contact</a></li>
      </ul>
    </div>
    <div class="footer-section">
      <h3>Follow Us</h3>
      <div class="social-links">
        <a href="/eventz_final/app/views/static/contact.php"><i class="fab fa-facebook"></i></a>
        <a href="/eventz_final/app/views/static/contact.php"><i class="fab fa-twitter"></i></a>
        <a href="/eventz_final/app/views/static/contact.php"><i class="fab fa-instagram"></i></a>
        <a href="/eventz_final/app/views/static/contact.php"><i class="fab fa-linkedin"></i></a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    © 2025 | EVENTZ.com | All rights reserved.
  </div>
</footer>

</body>
</html>