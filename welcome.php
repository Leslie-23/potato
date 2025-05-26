<!DOCTYPE html>
<html lang="en">
  <?php
// Database connection
require_once './dbcon.php';

// Fetch statistics for the live ticker and hero section
$activeTrainersQuery = "SELECT COUNT(*) FROM staffs WHERE designation = 'Trainer'";
$workoutPlansQuery = "SELECT COUNT(*) FROM workout_plan";
$activeMembersQuery = "SELECT COUNT(*) FROM members WHERE status = 'Active'";
$weeklySessionsQuery = "SELECT COUNT(*) FROM training_sessions WHERE session_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)";
$caloriesBurnedQuery = "SELECT SUM(duration) FROM attendance WHERE curr_date = CURDATE()";
$availableSpotsQuery = "SELECT COUNT(*) FROM training_sessions WHERE session_date > NOW() AND status = 'scheduled'";

$activeTrainers = $conn->query($activeTrainersQuery)->fetch_row()[0];
$activeMembers = $conn->query($activeMembersQuery)->fetch_row()[0];
$workoutPlans = $conn->query($workoutPlansQuery)->fetch_row()[0];
// $activeMembers = $conn->query($activeMembersQuery)->fetch_row()[0];
$weeklySessions = $conn->query($weeklySessionsQuery)->fetch_row()[0];
$caloriesBurned = $conn->query($caloriesBurnedQuery)->fetch_row()[0] * 10; // Assuming 10 cal/min
$availableSpots = $conn->query($availableSpotsQuery)->fetch_row()[0];

// Fetch equipment statistics
$equipmentStatsQuery = "SELECT 
    COUNT(*) as total_equipment,
    SUM(CASE WHEN status = 'good' THEN 1 ELSE 0 END) as working_equipment,
    SUM(CASE WHEN status = 'out_of_order' OR status = 'damaged' THEN 1 ELSE 0 END) as non_working_equipment
    FROM equipment";
$equipmentStats = $conn->query($equipmentStatsQuery)->fetch_assoc();
$equipmentUptime = round(($equipmentStats['working_equipment'] / $equipmentStats['total_equipment']) * 100);

// Fetch member progress statistics
$weightLossStatsQuery = "SELECT 
    AVG(ini_weight - curr_weight) as avg_weight_loss 
    FROM members 
    WHERE ini_weight > curr_weight AND curr_weight > 0";
$weightLossStats = $conn->query($weightLossStatsQuery)->fetch_assoc();
$avgWeightLoss = round($weightLossStats['avg_weight_loss'] ?? 0);

// Fetch trainers data
$trainersQuery = "SELECT s.*, t.specialization, t.years_experience 
                 FROM staffs s 
                 JOIN trainers t ON s.user_id = t.trainer_id 
                 WHERE s.designation = 'Trainer' 
                 LIMIT 3";
$trainers = $conn->query($trainersQuery);

// Fetch success stories from members with progress
$successStoriesQuery = "SELECT m.fullname, m.ini_weight, m.curr_weight, 
                       (m.ini_weight - m.curr_weight) as weight_loss,
                       m.ini_bodytype, m.curr_bodytype, m.progress_date
                       FROM members m
                       WHERE m.ini_weight > 0 AND m.curr_weight > 0 
                       AND m.ini_weight != m.curr_weight
                       ORDER BY weight_loss DESC
                       LIMIT 3";
$successStories = $conn->query($successStoriesQuery);

// Fetch next available session
$nextSessionQuery = "SELECT ts.session_id, ts.session_date, ts.end_date, 
                    m.fullname as member_name, s.fullname as trainer_name,
                    wp.workout_name, COUNT(a.id) as attendees
                    FROM training_sessions ts
                    JOIN members m ON ts.user_id = m.user_id
                    JOIN staffs s ON ts.trainer_id = s.user_id
                    JOIN workout_plan wp ON ts.table_id = wp.table_id
                    LEFT JOIN attendance a ON a.user_id = m.user_id AND DATE(a.curr_date) = DATE(ts.session_date)
                    WHERE ts.session_date > NOW() AND ts.status = 'scheduled'
                    GROUP BY ts.session_id
                    ORDER BY ts.session_date ASC
                    LIMIT 1";
$nextSession = $conn->query($nextSessionQuery)->fetch_assoc();

// Calculate spots left (assuming max 8 per session)
$spotsLeft = 8 - ($nextSession['attendees'] ?? 0);
?>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>EliteFit - Data-Driven Fitness Management</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="styles-2.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap"
      rel="stylesheet"
    />
  </head>
  <style>
    body{
      background-color: #2c3640;
    }
  .divvy{
      background-color: #2c3640 !important;
    }
  .divvy-1{
      background: linear-gradient(#2c3640, #2c3630) !important;
    }
  </style>
  <body>
    <div class="noise-overlay"></div>

    <!-- Navigation -->
   
    

    <!-- Hero Section -->
    <section class="hero divvy" >
      <div class="container">
        <div class="hero-content">
          <h1 class="gradient-text ">
            Transform Your <span class="gradient-text">Fitness Journey</span>
          </h1>
          <p class="hero-subtitle">
            Join the data-driven fitness revolution that's helping thousands
            achieve their goals faster
          </p>
<button class="cta-button primary bold-text text-big"><a href="./Gym-System/customer/index.php">Start Your Transformation</a></button>
<!-- <button class="cta-button primary bold-text text-big"><a href="./Gym-System/index.php">Start Your Transformation</a></button> -->
        </div></div>
        <!-- </section> -->

        <div class="hero-stats divvy">
          <div class="stat">
            <div class="stat-number">94%</div>
            <div class="stat-text">of members achieve their fitness goals</div>
          </div>
          <div class="stat">
            <div class="stat-number">3.2x</div>
            <div class="stat-text">faster results than traditional gyms</div>
          </div>
          <div class="stat">
            <div class="stat-number">87%</div>
            <div class="stat-text">report improved energy levels</div>
          </div>
        </div>
      </div>
    </section>

     <section class="cta-section">
      <div class="container">
        <div class="cta-card">
          <h2>
            Ready to Transform Your <span class="gradient-text">Fitness?</span>
          </h2>
          <p>
            Join EliteFit today and experience the future of fitness. Your
            personalized journey to a stronger, healthier you begins with a
            single step.
          </p>
          <div class="cta-buttons">
            <a href="./gym-system/customer/index.php" class="btn btn-primary">Create Account</a>
            <a href="./Gym-System/customer/login.php" class="btn btn-outline">Log In</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features divvy">
      <div class="container">
        <h2 class="gradient-text ">Why Choose <span class="gradient-text ">EliteFit</span></h2>
        <p class="section-subtitle">
          Experience the difference of data-driven fitness
        </p>

        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <h3>Personalized Programs</h3>
            <p>
              Custom fitness plans based on your goals, body type, and fitness
              level that evolve as you progress.
            </p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M12 2v4"></path>
                <path d="M12 18v4"></path>
                <path d="m4.93 4.93 2.83 2.83"></path>
                <path d="m16.24 16.24 2.83 2.83"></path>
                <path d="M2 12h4"></path>
                <path d="M18 12h4"></path>
                <path d="m4.93 19.07 2.83-2.83"></path>
                <path d="m16.24 7.76 2.83-2.83"></path>
              </svg>
            </div>
            <h3>Real-Time Progress Tracking</h3>
            <p>
              Monitor your improvements with precision using advanced biometric
              data and performance analytics.
            </p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M6.5 6.5h11"></path>
                <path d="M6.5 17.5h11"></path>
                <path d="M3 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M3 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
              </svg>
            </div>
            <h3>Premium Equipment</h3>
            <p>
              Access to state-of-the-art fitness equipment designed for optimal
              performance and results.
            </p>
          </div>

          <div class="feature-card">
            <div class="feature-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                width="24"
                height="24"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"
                ></path>
                <path d="m9 12 2 2 4-4"></path>
              </svg>
            </div>
            <h3>Expert Coaching</h3>
            <p>
              Learn from certified trainers who use data to optimize your form,
              technique, and overall progress.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials divvy-1">
      <div class="container">
        <h2 class="gradient-text ">Success <span class="gradient-text">Stories</span></h2>
        <p class="section-subtitle">Real results from real members</p>

        <div class="testimonial-slider">
          <div class="testimonial active">
            <div class="testimonial-content">
              <div class="testimonial-quote">
                "EliteFit completely changed my approach to fitness. The
                personalized program and data tracking helped me lose 32 pounds
                in just 6 months. I've never felt stronger or more confident."
              </div>
              <div class="testimonial-author">
                <div class="author-image"></div>
                <div class="author-details">
                  <div class="author-name">James Wilson</div>
                  <div class="author-achievement">Lost 32 lbs in 6 months</div>
                </div>
              </div>
            </div>
            <div class="testimonial-stats">
              <div class="testimonial-stat">
                <div class="stat-value">-14%</div>
                <div class="stat-label">Body Fat</div>
              </div>
              <div class="testimonial-stat">
                <div class="stat-value">+87%</div>
                <div class="stat-label">Strength</div>
              </div>
              <div class="testimonial-stat">
                <div class="stat-value">+23%</div>
                <div class="stat-label">VO2 Max</div>
              </div>
            </div>
          </div>
        </div>

        <div class="testimonial-dots">
          <span class="dot active"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
      <div class="container">
        <div class="about-content">
          <h2>About <span class="gradient-text">EliteFit</span></h2>
          <p>
            EliteFit is a revolutionary fitness management system that combines
            cutting-edge technology with proven exercise science. Our
            data-driven approach helps members achieve their fitness goals
            faster and more efficiently than traditional methods.
          </p>
          <p>
            Founded in 2023 by a team of fitness professionals and tech
            innovators, EliteFit has quickly become the preferred choice for
            those serious about their fitness journey.
          </p>
          <div class="about-stats">
            <div class="about-stat">
              <div class="stat-number"><?=  $workoutPlans?></div>
              <div class="stat-text">Workout Plans</div>
            </div>
            <div class="about-stat">
              <div class="stat-number"><?=  $activeTrainers ?></div>
              <div class="stat-text">Expert Trainers</div>
            </div>
            <div class="about-stat">
              <div class="stat-number"><?=  $activeMembers ?></div>
              <div class="stat-text">Active Members</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Final CTA Section -->
    <section class="cta-section">
      <div class="container">
        <div class="cta-card">
          <h2>
            Ready to Transform Your <span class="gradient-text">Fitness?</span>
          </h2>
          <p>
            Join EliteFit today and experience the future of fitness. Your
            personalized journey to a stronger, healthier you begins with a
            single step.
          </p>
          <div class="cta-buttons">
            <a href="./gym-system/customer/index.php" class="btn btn-primary">Create Account</a>
            <a href="./Gym-System/customer/login.php" class="btn btn-outline">Log In</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <div class="container">
      <div class="footer-content">
        <div class="footer-logo">EliteFit</div>
        <div class="footer-links">
          <div class="footer-column">
            <h4>Company</h4>
            <a href="about.html">About Us</a>
            <a href="#">Careers</a>
            <a href="#">Press</a>
          </div>
          <div class="footer-column">
            <h4>Resources</h4>
            <a href="#">Blog</a>
            <a href="#">Nutrition Guide</a>
            <a href="#">Workout Library</a>
          </div>
          <div class="footer-column">
            <h4>Legal</h4>
            <a href="legal.html#privacy-policy">Privacy Policy</a>
            <a href="legal.html#terms-of-service">Terms of Service</a>
            <a href="legal.html#cookie-policy">Cookie Policy</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> EliteFit Fitness. All rights reserved.</p>
        <div class="social-links">
          <a href="#" class="social-icon">
            <svg viewBox="0 0 24 24" width="24" height="24">
              <path fill="currentColor" d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z" />
            </svg>
          </a>
          <a href="#" class="social-icon">
            <svg viewBox="0 0 24 24" width="24" height="24">
              <path fill="currentColor" d="M22.46,6C21.69,6.35 20.86,6.58 20,6.69C20.88,6.16 21.56,5.32 21.88,4.31C21.05,4.81 20.13,5.16 19.16,5.36C18.37,4.5 17.26,4 16,4C13.65,4 11.73,5.92 11.73,8.29C11.73,8.63 11.77,8.96 11.84,9.27C8.28,9.09 5.11,7.38 3,4.79C2.63,5.42 2.42,6.16 2.42,6.94C2.42,8.43 3.17,9.75 4.33,10.5C3.62,10.5 2.96,10.3 2.38,10C2.38,10 2.38,10 2.38,10.03C2.38,12.11 3.86,13.85 5.82,14.24C5.46,14.34 5.08,14.39 4.69,14.39C4.42,14.39 4.15,14.36 3.89,14.31C4.43,16 6,17.26 7.89,17.29C6.43,18.45 4.58,19.13 2.56,19.13C2.22,19.13 1.88,19.11 1.54,19.07C3.44,20.29 5.7,21 8.12,21C16,21 20.33,14.46 20.33,8.79C20.33,8.6 20.33,8.42 20.32,8.23C21.16,7.63 21.88,6.87 22.46,6Z" />
            </svg>
          </a>
          <a href="#" class="social-icon">
            <svg viewBox="0 0 24 24" width="24" height="24">
              <path fill="currentColor" d="M19,3A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5A2,2 0 0,1 5,3H19M18.5,18.5V13.2A3.26,3.26 0 0,0 15.24,9.94C14.39,9.94 13.4,10.46 12.92,11.24V10.13H10.13V18.5H12.92V13.57C12.92,12.8 13.54,12.17 14.31,12.17A1.4,1.4 0 0,1 15.71,13.57V18.5H18.5M6.88,8.56A1.68,1.68 0 0,0 8.56,6.88C8.56,5.95 7.81,5.19 6.88,5.19A1.69,1.69 0 0,0 5.19,6.88C5.19,7.81 5.95,8.56 6.88,8.56M8.27,18.5V10.13H5.5V18.5H8.27Z" />
            </svg>
</a>
</div>
</div>
</div>

</footer> 

    <script src="script.js"></script>
  </body>
</html>
