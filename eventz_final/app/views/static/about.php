<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Eventz</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f5f6fa;
      color: #333;
      margin: 0;
      padding: 0;
      animation: fadeIn 0.8s ease-in-out;
    }

    .container2 {
      max-width: 1200px;
      margin: 60px auto 0 auto;
      background: #fff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
      text-align: center;
    }

    .container2 h1 a {
      color: #771ae8;
      text-decoration: none;
      letter-spacing: 3px;
    }

    .container {
      max-width: 900px;
      margin: 60px auto 100px auto;
      background: #fff;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      transition: transform 0.3s ease;
    }

    .container:hover {
      transform: translateY(-5px);
    }

    h1 {
      color: #771ae8;
      text-align: center;
      margin-bottom: 20px;
    }

    p {
      line-height: 1.8;
      font-size: 16px;
      text-align: justify;
    }

    a {
      color: #ffffffff;
      text-decoration: none;
      transition: color 0.3s ease;
    }
    

    a:hover {
      color: #ffffffff;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    /* Footer Styles */
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
      flex: none;
      min-width: 150px;
      margin: 15px 0px;
    }

    .footer-section h3 {
      font-size: 20px;
      margin-bottom: 20px;
      color: #fff;
    }

    .footer-section ul {
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

    .footer-bottom {
      text-align: center;
      margin-top: 40px;
      padding-top: 20px;
      border-top: 1px solid #2e3745;
      color: #cfd8e3;
    }

    /* Centered Navbar Styling */
    .authnavbar {
      background: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      padding: 20px 0;
      position: sticky;
      z-index: 10;
    }

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
</head>
<body>

<nav class="authnavbar">
  <div class="authnavbar-center">
    <h1><a href="/eventz_final">E V E N T Z.com</a></h1>
  </div>
</nav>

  <div class="container">
    <h1>About Eventz</h1>
    <p>
      Eventz.com is a dynamic platform built to connect participants, organizers, sponsors, and administrators seamlessly.
      It enables efficient event management, user engagement, and collaboration within one unified system.
    </p>
    <p>
      Our goal is to simplify the process of organizing and joining events through a smart, user-friendly interface.
    </p>
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
