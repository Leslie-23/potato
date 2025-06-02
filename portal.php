<?php
// Database connection (if needed for future enhancements)
require_once('dbcon.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Access Portal | EliteFit</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/font-awesome.css" />
    <link rel="stylesheet" href="style-2.css" />
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
      :root {
        --primary: #2c3e50;
        --secondary: #00cae9;
        --light: #2c3640;
        --dark: #343a40;
        --gray: #7f8c8d;
      }
      
      body {
        font-family: 'Open Sans', sans-serif;
        background-color: var(--light);
        background-color: var(--primary);
        color: var(--primary);
        line-height: 1.6;
    }
    
    .glass-nav {
        background: rgba(255, 255, 255, 0.1);
        background-color: var(--primary);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        position: sticky;
        top: 0;
        z-index: 1000;
      }
      
      .nav-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 2rem;
        max-width: 1200px;
        margin: 0 auto;
      }
      
      .logo {
        font-size: 1.5rem;
        font-weight: 800;
        color: white;
      }
      
      .nav-links {
        display: flex;
        gap: 2rem;
      }
      
      .nav-links a {
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: opacity 0.3s;
      }
      
      .nav-links a:hover {
        opacity: 0.8;
      }
      
      .nav-links a.active {
        border-bottom: 2px solid white;
      }
      
      .page-header {
        padding: 6rem 0 4rem;
        text-align: center;
        background: linear-gradient(135deg, #2c3e50, #4ca1af);
        color: white;
      }
      
      .page-header h1 {
        font-size: 3rem;
        margin-bottom: 1rem;
      }
      
      .gradient-text {
        background: linear-gradient(90deg, #009db5, #00cae9);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
      }
      
      .portal-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 4rem 2rem;
      }
      
      .section-title {
        text-align: center;
        font-size: 2.5rem;
        margin-bottom: 3rem;
        color: var(--primary);
      }
      
      .roles-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
      }
      
      .role-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        text-align: center;
        padding: 2rem 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
      }
      
      .role-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      }
      
      .role-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--secondary), #009db5);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        color: white;
        font-size: 2rem;
      }
      
      .role-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: var(--primary);
      }
      
      .role-desc {
        color: var(--gray);
        margin-bottom: 1.5rem;
        font-size: 0.9rem;
      }
      
      .role-btn {
        display: inline-block;
        padding: 0.75rem 1.5rem;
        background: linear-gradient(to right, var(--secondary), #009db5);
        color: white;
        text-decoration: none;
        border-radius: 30px;
        font-weight: 600;
        transition: transform 0.3s, box-shadow 0.3s;
        margin-top: auto;
      }
      
      .role-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(231, 76, 60, 0.3);
        color: white;
      }
      
      .cta-button {
    background: linear-gradient(45deg, #00cae9, #009db5);
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 5px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s ease;
    text-decoration: none;
}

.cta-button:hover {
    transform: translateY(-2px);
}
      footer {
        background: var(--dark);
        color: white;
        padding: 3rem 0;
      }
      
      .footer-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 2rem;
      }
      
      .footer-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
      }
      
      .footer-logo {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 1rem;
      }
      
      .footer-column h4 {
        font-size: 1rem;
        margin-bottom: 1rem;
        color: white;
      }
      
      .footer-column a {
        display: block;
        color: #adb5bd;
        margin-bottom: 0.5rem;
        text-decoration: none;
        transition: color 0.3s;
      }
      
      .footer-column a:hover {
        color: white;
      }
      
      .footer-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
      }
      
      .social-links {
        display: flex;
        gap: 1rem;
      }
      
      .social-icon {
        color: white;
        transition: color 0.3s;
      }
      
      .social-icon:hover {
        color: var(--secondary);
      }
      
      @media (max-width: 768px) {
        .nav-links {
          gap: 1rem;
        }
        
        .page-header h1 {
          font-size: 2.5rem;
        }
        
        .roles-grid {
          grid-template-columns: 1fr;
        }
      }
    </style>
</head>
<body>
    <nav class="glass-nav">
      <div class="nav-container">
        <div class="logo">EliteFit</div>
        <div class="nav-links">
          <a href="./index.php">Home</a>
          <!-- <a href="./about.html">About</a> -->
          <!-- <a href="./trainers.php">Trainers</a> -->
          <!-- <a href="./press.html">Press</a> -->
          <a href="./portal.php" class="active">Portal</a>
        </div>
         <a href="./gym-system/customer/index.php " class="cta-button">
      <!-- <button class="cta-button primary bold-text text-sm"> -->
        Join Now
      <!-- </button> -->
    </a>
      </div>
    </nav>

    <section class="page-header">
      <div class="container">
        <h1 ><span class="gradient-text">EliteFit</span> Portal</h1>
        <h3 class="gradient-text">Select your role to access the system</h3>
      </div>
    </section>

    <div class="portal-container">
      <h2 class="section-title">System Access</h2>
      
      <div class="roles-grid">
        <!-- Admin Card -->
        <div class="role-card">
          <div class="role-icon">
            <i class="fas fa-user-shield"></i>
          </div>
          <h3 class="role-name">Administrator</h3>
          <p class="role-desc">Full system access and configuration privileges</p>
          <a href="./gym-system/admin/index.php" class="role-btn">Login</a>
        </div>
        
        <!-- Manager Card -->
        <div class="role-card">
          <div class="role-icon">
            <i class="fas fa-clipboard-list"></i>
          </div>
          <h3 class="role-name">Manager</h3>
          <p class="role-desc">Staff management and operational oversight</p>
          <a href="./gym-system/manager/index.php" class="role-btn">Login</a>
        </div>
        
        <!-- Trainer Card -->
        <div class="role-card">
          <div class="role-icon">
            <i class="fas fa-dumbbell"></i>
          </div>
          <h3 class="role-name">Trainer</h3>
          <p class="role-desc">Client sessions and workout planning</p>
          <a href="./gym-system/trainer/index.php" class="role-btn">Login</a>
        </div>
        
        <!-- Staff Card -->
        <div class="role-card">
          <div class="role-icon">
            <i class="fas fa-users"></i>
          </div>
          <h3 class="role-name">Staff</h3>
          <p class="role-desc">General staff access and member support</p>
          <a href="./gym-system/staff/index.php" class="role-btn">Login</a>
        </div>
        
        <!-- Cashier Card -->
        <div class="role-card">
          <div class="role-icon">
            <i class="fas fa-cash-register"></i>
          </div>
          <h3 class="role-name">Cashier</h3>
          <p class="role-desc">Payment processing and membership management</p>
          <a href="./gym-system/cashier/index.php" class="role-btn">Login</a>
        </div>
        
        <!-- Equipment Manager Card -->
        <div class="role-card">
          <div class="role-icon">
            <i class="fas fa-tools"></i>
          </div>
          <h3 class="role-name">Equipment Manager</h3>
          <p class="role-desc">Maintenance tracking and inventory management</p>
          <a href="./gym-system/equipment-manager/index.php" class="role-btn">Login</a>
        </div>
      </div>
    </div>

    <footer>
      <div class="footer-container">
        <div class="footer-content">
          <div class="footer-column">
            <div class="footer-logo">EliteFit</div>
          </div>
          <div class="footer-column">
            <h4>Company</h4>
            <a href="about.html">About Us</a>
            <a href="careers.html">Careers</a>
            <a href="press.html">Press</a>
            <a href="portal.php">Management Portal</a>
          </div>
          <div class="footer-column">
            <h4>Resources</h4>
            <a href="https://medium.com/tag/fitness">Blog</a>
            <a href="https://medium.com/@drjasonfung/control-hunger-not-calories-95c9076710f0">Nutrition Guide</a>
            <a href="https://medium.com/tag/workout-routines">Workout Library</a>
          </div>
          <div class="footer-column">
            <h4>Legal</h4>
            <a href="legal.html#privacy-policy">Privacy Policy</a>
            <a href="legal.html#terms-conditions">Terms of Service</a>
            <a href="legal.html#cookie-policy">Cookie Policy</a>
          </div>
        </div>
        <div class="footer-bottom">
          <p>&copy; 2025 EliteFit Fitness. All rights reserved.</p>
          <div class="social-links">
            <a href="https://instagram.com/elitefit" target="_blank" class="social-icon">
              <i class="fab fa-instagram"></i>
            </a>
            <a href="https://x.com/elitefit" target="_blank" class="social-icon">
              <i class="fab fa-twitter"></i>
            </a>
            <a href="https://linkedin.com/elitefit" target="_blank" class="social-icon">
              <i class="fab fa-linkedin"></i>
            </a>
          </div>
        </div>
      </div>
    </footer>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>