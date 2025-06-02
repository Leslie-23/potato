<?php
// Database connection
require_once('dbcon.php');

// Fetch all trainers with their specializations and schedules
$query = "SELECT 
            s.user_id, 
            s.fullname, 
            s.image_url, 
            t.specialization, 
            t.bio, 
            t.certification,
            t.years_experience,
            GROUP_CONCAT(DISTINCT wp.workout_name SEPARATOR ', ') AS specialties,
            GROUP_CONCAT(DISTINCT CONCAT(
              DATE_FORMAT(ts.session_date, '%a %b %e, %Y %l:%i %p'), 
              ' - ', 
              wp.workout_name
            ) SEPARATOR '||') AS upcoming_sessions
          FROM staffs s
          JOIN trainers t ON s.user_id = t.trainer_id
          LEFT JOIN trainer_workout_specialization tws ON t.trainer_id = tws.trainer_id
          LEFT JOIN workout_plan wp ON tws.plan_id = wp.table_id
          LEFT JOIN training_sessions ts ON t.trainer_id = ts.trainer_id AND ts.status = 'scheduled'
          WHERE s.designation = 'Trainer'
          GROUP BY s.user_id
          ORDER BY s.fullname";

$result = mysqli_query($conn, $query);
$trainers = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Our Trainers | EliteFit</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/font-awesome.css" />
    <link rel="stylesheet" href="../css/matrix-style.css" />
    <link rel="stylesheet" href="style-2.css" />
    <link rel="stylesheet" href="style.css" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
      :root {
        --primary: #2c3e50;
        --secondary: #2c3e50;
        --light: #f8f9fa;
        --dark: #343a40;
        --gray: #7f8c8d;
      }
      
      body {
        font-family: 'Open Sans', sans-serif;
        background-color: var(--primary);
        color: var(--light);
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
        color: #000;
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
      
      /* .gradient-text {
        background: linear-gradient(90deg, #e74c3c, #f39c12);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
      } */
      
      .trainers-container {
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
      
      .trainers-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 2rem;
      }
      
      .trainer-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      
      .trainer-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      }
      
      .trainer-image {
        height: 300px;
        overflow: hidden;
      }
      
      .trainer-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
      }
      
      .trainer-card:hover .trainer-image img {
        transform: scale(1.05);
      }
      
      .trainer-info {
        padding: 1.5rem;
      }
      
      .trainer-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
      }
      
      .trainer-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
        color: var(--primary);
      }
      
      .trainer-exp {
        background: var(--secondary);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
      }
      
      .trainer-specialty {
        color: var(--secondary);
        font-weight: 600;
        margin-bottom: 1rem;
        display: block;
      }
      
      .trainer-bio {
        color: var(--gray);
        margin-bottom: 1.5rem;
      }
      
      .collapse-section {
        margin-top: 1rem;
        border-top: 1px solid #eee;
        padding-top: 1rem;
      }
      
      .collapse-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        padding: 0.5rem 0;
      }
      
      .collapse-header h4 {
        margin: 0;
        font-size: 1rem;
        color: var(--primary);
      }
      
      .collapse-icon {
        transition: transform 0.3s ease;
      }
      
      .collapse-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
      }
      
      .collapse-content-inner {
        padding: 1rem 0;
      }
      
      .badge-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
      }
      
      .specialty-badge {
        background: #f1f1f1;
        color: var(--primary);
        padding: 0.3rem 0.8rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
      }
      
      .session-list {
        list-style: none;
        padding: 0;
        margin: 0;
      }
      
      .session-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #eee;
      }
      
      .session-item:last-child {
        border-bottom: none;
      }
      
      .session-time {
        font-weight: 600;
        color: var(--primary);
      }
      
      .session-program {
        color: var(--gray);
        font-size: 0.9rem;
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
        
        .trainers-grid {
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
          <a href="./index.php" title="Go to Home">Home</a>
           <!--<a href="./about.html">About</a> -->
          <a href="./trainers.php" class="active">Trainers</a>
          <!-- <a href="./press.html">Press</a> -->
        </div>
       <a
            href="./gym-system/customer/index.php"
            class="cta-button"
            style="
              display: inline-block;
              padding: 0.75rem 1.5rem;
              background: linear-gradient(to right, #10b981, #3b82f6);
              color: white;
              text-decoration: none;
              border: none;
              border-radius: 0.375rem;
              font-weight: bold;
              font-size: 1rem;
            "
          >
            Join Now
          </a>
      </div>
    </nav>

    <section class="page-header">
      <div class="container">
        <h1>Meet Our <span class="gradient-text">Trainers</span></h1>
        <h3 class="gradient-text">World-class coaching from certified professionals</h3>
      </div>
    </section>

    <div class="trainers-container">
      <h2 class="section-title">Our Expert Coaching Team</h2>
      
      <div class="trainers-grid">
        <?php foreach ($trainers as $trainer): 
          $sessions = !empty($trainer['upcoming_sessions']) ? explode('||', $trainer['upcoming_sessions']) : [];
        ?>
        <div class="trainer-card">
          <div class="trainer-image">
            <img src="<?= htmlspecialchars($trainer['image_url'] ?: 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80') ?>" alt="<?= htmlspecialchars($trainer['fullname']) ?>">
          </div>
          <div class="trainer-info">
            <div class="trainer-header">
              <h3 class="trainer-name"><?= htmlspecialchars($trainer['fullname']) ?></h3>
              <span class="trainer-exp"><?= htmlspecialchars($trainer['years_experience']) ?> yrs</span>
            </div>
            <span class="trainer-specialty"><?= htmlspecialchars($trainer['specialization']) ?></span>
            <p class="trainer-bio"><?= htmlspecialchars($trainer['bio']) ?></p>
            
            <!-- Certifications Collapse -->
            <div class="collapse-section">
              <div class="collapse-header" onclick="toggleCollapse(this)">
                <h4>Certifications</h4>
                <i class="fas fa-chevron-down collapse-icon"  style="color: #2c3e50;"></i>
              </div>
              <div class="collapse-content">
                <div class="collapse-content-inner" style="color: var(--gray);">
                  <p><?= htmlspecialchars($trainer['certification']) ?></p>
                </div>
              </div>
            </div>
            
            <!-- Specialties Collapse -->
            <div class="collapse-section">
              <div class="collapse-header" onclick="toggleCollapse(this)">
                <h4>Specialties</h4>
                <i class="fas fa-chevron-down collapse-icon" style="color: #2c3e50;"></i>
              </div>
              <div class="collapse-content">
                <div class="collapse-content-inner">
                  <div class="badge-container">
                    <?php 
                    $specialties = explode(', ', $trainer['specialties']);
                    foreach ($specialties as $specialty): 
                      if (!empty(trim($specialty))):
                    ?>
                      <span class="specialty-badge"><?= htmlspecialchars(trim($specialty)) ?></span>
                    <?php 
                      endif;
                    endforeach; 
                    ?>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Upcoming Sessions Collapse -->
            <?php if (!empty($sessions)): ?>
            <div class="collapse-section">
              <div class="collapse-header" onclick="toggleCollapse(this)">
                <h4>Upcoming Sessions</h4>
                <i class="fas fa-chevron-down collapse-icon"  style="color: #2c3e50;"></i>
              </div>
              <div class="collapse-content">
                <div class="collapse-content-inner">
                  <ul class="session-list">
                    <?php foreach ($sessions as $session): 
                      if (!empty(trim($session))):
                        $parts = explode(' - ', $session, 2);
                    ?>
                      <li class="session-item">
                        <div class="session-time"><?= htmlspecialchars($parts[0] ?? '') ?></div>
                        <div class="session-program"><?= htmlspecialchars($parts[1] ?? '') ?></div>
                      </li>
                    <?php 
                      endif;
                    endforeach; 
                    ?>
                  </ul>
                </div>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
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

    <script>
      // Collapse functionality
      function toggleCollapse(header) {
        const content = header.nextElementSibling;
        const icon = header.querySelector('.collapse-icon');
        
        if (content.style.maxHeight) {
          content.style.maxHeight = null;
          icon.style.transform = 'rotate(0deg)';
        } else {
          content.style.maxHeight = content.scrollHeight + 'px';
          icon.style.transform = 'rotate(180deg)';
        }
      }
      
      // Initialize collapses to be closed
      document.addEventListener('DOMContentLoaded', function() {
        const collapses = document.querySelectorAll('.collapse-content');
        collapses.forEach(collapse => {
          collapse.style.maxHeight = '0';
        });
      });
    </script>
</body>
</html>