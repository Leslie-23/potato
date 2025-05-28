<?php
// Database connection
require_once './dbcon.php';

// Fetch statistics for the live ticker and hero section
$activeTrainersQuery = "SELECT COUNT(*) FROM staffs WHERE designation = 'Trainer'";
$activeMembersQuery = "SELECT COUNT(*) FROM members WHERE status = 'Active'";
$weeklySessionsQuery = "SELECT COUNT(*) FROM training_sessions WHERE session_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)";
$caloriesBurnedQuery = "SELECT SUM(duration) FROM attendance WHERE curr_date = CURDATE()";
$availableSpotsQuery = "SELECT COUNT(*) FROM training_sessions WHERE session_date > NOW() AND status = 'scheduled'";

$activeTrainers = $conn->query($activeTrainersQuery)->fetch_row()[0];
$activeMembers = $conn->query($activeMembersQuery)->fetch_row()[0];
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

// Fetch trainers data (updated to include image_url)
$trainersQuery = "SELECT s.fullname, s.designation, s.image_url, 
                  t.specialization, t.years_experience 
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
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EliteFit | Next-Gen Fitness</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <style>
.contact-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2rem;
  margin-bottom: 3rem;
}

.contact-card {
  background: #2c3640;
  padding: 2rem;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 10px 20px rgba(0,0,0,0.05);
  transition: transform 0.3s ease;
}

.contact-card:hover {
  transform: translateY(-5px);
}

.contact-icon {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  margin: 0 auto 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 1.5rem;
}

.contact-btn {
  display: inline-block;
  padding: 12px 30px;
  color: white!important;
  border-radius: 30px;
  text-decoration: none;
  margin-top: 1rem;
  transition: filter 0.3s ease;
}

.contact-btn:hover {
  filter: brightness(110%);
}

.map-container {
  margin-top: 3rem;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
</style>
  <style>
    .trainer-image {
  width: 100%;
  height: 300px;
  border-radius: 15px 15px 0 0;
  background-repeat: no-repeat;
}
    /* Additional dynamic styling based on data */
    .progress-circle {
      animation: fillProgress 1.5s ease-in-out forwards;
    }
    
    @keyframes fillProgress {
      from { stroke-dashoffset: 283; }
      to { stroke-dashoffset: <?= 283 - (283 * ($equipmentUptime / 100)) ?>; }
    }
    
    .satisfaction-rate .progress-circle {
      animation-name: fillSatisfaction;
    }
    
    @keyframes fillSatisfaction {
      from { stroke-dashoffset: 283; }
      to { stroke-dashoffset: <?= 283 - (283 * 0.85) ?>; }
    }
  </style>
  <style>
/* Navigation */
.glass-nav {
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(5px);
    position: fixed;
    width: 100%;
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
    font-weight: 700;
    color: white;
    z-index: 1001;
}

.nav-links {
    display: flex;
    gap: 2rem;
    align-items: center;
    transition: 0.3s ease;
}

.nav-links a {
    color: white;
    text-decoration: none;
    font-weight: 500;
    transition: opacity 0.3s ease;
}

.nav-links a:hover {
    opacity: 0.8;
}

.cta-button {
    background: linear-gradient(45deg, #ff5733, #ff9933);
    border: none;
    padding: 0.8rem 1.5rem;
    border-radius: 5px;
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.cta-button:hover {
    transform: translateY(-2px);
}

/* Mobile Menu Toggle */
.menu-toggle {
    display: none;
    cursor: pointer;
    z-index: 1001;
}

.hamburger {
    width: 25px;
    height: 3px;
    background-color: white;
    margin: 5px 0;
    transition: 0.4s;
}

.mid{
  display: flex;
  justify-content: space-evenly;  /* Distributes space evenly between items */
  align-items: center;            /* Centers vertically */
  width: 100%;                    /* Ensures full width for proper spacing */
  margin: 0 auto;   
}
/* Mobile Styles */
@media screen and (max-width: 768px) {
    .menu-toggle {
        display: block;
    }

    .nav-links {
        position: fixed;
        top: 0;
        right: -100%;
        height: 100vh;
        width: 70%;
        background: rgba(0, 0, 0, 0.9);
        backdrop-filter: blur(10px);
        flex-direction: column;
        justify-content: center;
        align-items: center;
        transition: right 0.3s ease;
        padding: 2rem;
    }

    .nav-links.active {
        right: 0;
    }

    .cta-button {
        margin-top: 2rem;
    }

    .menu-toggle.active .hamburger:nth-child(1) {
        transform: rotate(-45deg) translate(-5px, 6px);
    }

    .menu-toggle.active .hamburger:nth-child(2) {
        opacity: 0;
    }

    .menu-toggle.active .hamburger:nth-child(3) {
        transform: rotate(45deg) translate(-5px, -6px);
    }

}
</style>
<style>
.modal {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 1000;
}

.modal-content {
  background-color: #2c3640;
  margin: 15% auto;
  padding: 2rem;
  width: 70%;
  max-width: 800px;
  position: relative;
  border-radius: 10px;
}

.close {
  position: absolute;
  right: 1rem;
  top: 0.5rem;
  font-size: 2rem;
  cursor: pointer;
}

.close:hover {
  color: #666;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Modal handling
  const links = document.querySelectorAll('.insight-link');
  const modals = document.querySelectorAll('.modal');
  const closeBtns = document.querySelectorAll('.close');

  links.forEach(link => {
    link.addEventListener('click', (e) => {
      e.preventDefault();
      const target = link.getAttribute('href').substring(1);
      document.getElementById(target).style.display = 'block';
    });
  });

  closeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      btn.closest('.modal').style.display = 'none';
    });
  });

  window.addEventListener('click', (e) => {
    if (e.target.classList.contains('modal')) {
      e.target.style.display = 'none';
    }
  });
});
</script>

<!-- Add this JavaScript -->
<script>
document.querySelector('.menu-toggle').addEventListener('click', function() {
    this.classList.toggle('active');
    document.querySelector('.nav-links').classList.toggle('active');
});

// Close menu when clicking outside or on a link
document.addEventListener('click', function(event) {
    const navLinks = document.querySelector('.nav-links');
    const menuToggle = document.querySelector('.menu-toggle');
    
    if (!event.target.closest('.nav-container') && navLinks.classList.contains('active')) {
        menuToggle.classList.remove('active');
        navLinks.classList.remove('active');
    }
});

document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        document.querySelector('.menu-toggle').classList.remove('active');
        document.querySelector('.nav-links').classList.remove('active');
    });
});
</script>
</head>
<body>
  <div class="noise-overlay"></div>
  
  <!-- Navigation -->
   <div class="md">

  
  <nav class="glass-nav">
    <div class="container nav-container">
      <div class="logo">EliteFit</div>
      
       <div class="menu-toggle">
            <div class="hamburger"></div>
            <div class="hamburger"></div>
            <div class="hamburger"></div>
        </div>
      <div class="nav-links ">
        <a href="#home" class="">Home</a>
        <a href="#why-us " class="">Why Us</a>
        <a href="#trainers " class="">Trainers</a>
        <a href="#success " class="">Success Stories</a>
        <a href="#contact " class="">Contact Us</a>
      </div>
      <button class="cta-button primary bold-text text-sm"><a href="./gym-system/customer/index.php">Join Now</a></button>
    </div>
  </nav>
   </div>

  <!-- Live Stats Ticker -->
  <!-- <div class="live-stats-ticker" style="margin-bottom: 20px;">
    <div class="ticker-container">
      <div class="ticker-item">
        <span class="ticker-icon">⚡</span>
        <span id="active-trainers"><?= $activeTrainers ?></span> certified trainers on staff
      </div>
      <div class="ticker-item">
        <span class="ticker-icon">🏋️‍♂️</span>
        <span id="available-spots"><?= $availableSpots ?></span> open spots in upcoming classes
      </div>
      <div class="ticker-item">
        <span class="ticker-icon">🔥</span>
        <span id="calories-burned"><?= $caloriesBurned ?></span> calories burned by members today
      </div>
    </div>
  </div> -->

  <!-- Hero Section -->
  <section id="home" class="hero">
    <div class="container">
      <div class="hero-content">
        <h1>Redefine Your <span class="gradient-text">Limits</span></h1>
        <p>The future of fitness is data-driven, personalized, and measurable.</p>
        
        <div class="stats-container">
          <div class="stat-card">
            <div class="stat-icon">
              <svg viewBox="0 0 24 24" width="32" height="32">
                <path fill="currentColor" d="M12,5.5A3.5,3.5 0 0,1 15.5,9A3.5,3.5 0 0,1 12,12.5A3.5,3.5 0 0,1 8.5,9A3.5,3.5 0 0,1 12,5.5M5,8C5.56,8 6.08,8.15 6.53,8.42C6.38,9.85 6.8,11.27 7.66,12.38C7.16,13.34 6.16,14 5,14A3,3 0 0,1 2,11A3,3 0 0,1 5,8M19,8A3,3 0 0,1 22,11A3,3 0 0,1 19,14C17.84,14 16.84,13.34 16.34,12.38C17.2,11.27 17.62,9.85 17.47,8.42C17.92,8.15 18.44,8 19,8M5.5,18.25C5.5,16.18 8.41,14.5 12,14.5C15.59,14.5 18.5,16.18 18.5,18.25V20H5.5V18.25M0,20V18.5C0,17.11 1.89,15.94 4.45,15.6C3.86,16.28 3.5,17.22 3.5,18.25V20H0M24,20H20.5V18.25C20.5,17.22 20.14,16.28 19.55,15.6C22.11,15.94 24,17.11 24,18.5V20Z" />
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value" id="active-members"><?= $activeMembers ?></div>
              <div class="stat-label">Active Members</div>
            </div>
          </div>
          
          <div class="stat-card">
            <div class="stat-icon">
              <svg viewBox="0 0 24 24" width="32" height="32">
                <path fill="currentColor" d="M7,5H21V7H7V5M7,13V11H21V13H7M4,4.5A1.5,1.5 0 0,1 5.5,6A1.5,1.5 0 0,1 4,7.5A1.5,1.5 0 0,1 2.5,6A1.5,1.5 0 0,1 4,4.5M4,10.5A1.5,1.5 0 0,1 5.5,12A1.5,1.5 0 0,1 4,13.5A1.5,1.5 0 0,1 2.5,12A1.5,1.5 0 0,1 4,10.5M7,19V17H21V19H7M4,16.5A1.5,1.5 0 0,1 5.5,18A1.5,1.5 0 0,1 4,19.5A1.5,1.5 0 0,1 2.5,18A1.5,1.5 0 0,1 4,16.5Z" />
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value" id="weekly-sessions"><?= $weeklySessions ?></div>
              <div class="stat-label">Training Sessions This Week</div>
            </div>
          </div>
        </div>

        <button class="cta-button primary bold-text text-big"><a href="./welcome.php">Start Your Transformation</a></button>
      </div>
      
      <div class="hero-visual">
        <div class="abstract-shape"></div>
        <div class="abstract-shape"></div>
        <div class="abstract-shape"></div>
      </div>
    </div>
  </section>

  <!-- Why Us Section -->
  <section id="why-us" class="why-us">
    <div class="my-padder"></div>
    <div class="container">
      <h2>Why <span class="gradient-text">EliteFit</span></h2>
      <p class="section-subtitle">Data-driven excellence that delivers results</p>
      
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-visual">
            <svg class="equipment-uptime" viewBox="0 0 100 100" width="80" height="80">
              <circle cx="50" cy="50" r="45" fill="none" stroke="#1d262e" stroke-width="8" />
              <circle cx="50" cy="50" r="45" fill="none" stroke="#4CAF50" stroke-width="8" stroke-dasharray="283" stroke-dashoffset="283" class="progress-circle" />
              <text x="50" y="55" text-anchor="middle" class="percentage"><?= $equipmentUptime ?>%</text>
            </svg>
          </div>
          <h3>Equipment Uptime</h3>
          <p>Our <?= $equipmentStats['total_equipment'] ?> state-of-the-art equipment items are maintained at peak performance, with <?= $equipmentStats['working_equipment'] ?> currently operational (<?= $equipmentUptime ?>% uptime).</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-visual">
            <div class="weight-loss">
              <span id="avg-weight-loss"><?= $avgWeightLoss ?></span>
              <span class="unit">lbs</span>
            </div>
          </div>
          <h3>Average Weight Loss</h3>
          <p>Our members see real results. On average, members lose <?= $avgWeightLoss ?> lbs within their first 3 months with our program.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-visual">
            <svg class="satisfaction-rate" viewBox="0 0 100 100" width="80" height="80">
              <circle cx="50" cy="50" r="45" fill="none" stroke="#1d262e" stroke-width="8" />
              <circle cx="50" cy="50" r="45" fill="none" stroke="#2196F3" stroke-width="8" stroke-dasharray="283" stroke-dashoffset="283" class="progress-circle" />
              <text x="50" y="55" text-anchor="middle" class="percentage">85%</text>
            </svg>
          </div>
          <h3>Member Satisfaction</h3>
          <p>Based on recent surveys, 85% of our members report being highly satisfied with their results and the community we've built.</p>
        </div>
        
        <div class="feature-card">
          <div class="feature-visual">
            <div class="trainer-ratio">
              <span>1:<?= round($activeMembers / $activeTrainers) ?></span>
            </div>
          </div>
          <h3>Trainer to Member Ratio</h3>
          <p>With <?= $activeTrainers ?> trainers for <?= $activeMembers ?> members (1:<?= round($activeMembers / $activeTrainers) ?> ratio), you get personal attention in every session.</p>
        </div>
      </div>
    </div>
  </section>

 <!-- Trainers Section -->
<section id="trainers" class="trainers">
  <div class="my-padder"></div>
  <div class="container">
    <h2>Elite <span class="gradient-text">Trainers</span></h2>
    <p class="section-subtitle">Experts in their fields, dedicated to your success</p>
    
    <div class="trainers-grid">
      <?php while($trainer = $trainers->fetch_assoc()): 
        $specializations = explode(',', $trainer['specialization']);
      ?>
      <div class="trainer-card">
        <!-- Updated image section -->
        <div class="trainer-image" 
             style="background-image: url('<?= $trainer['image_url'] ?>');
                   background-size: cover;
                   background-position: center;">
        </div>
        <h3><?= $trainer['fullname'] ?></h3>
        <p class="trainer-title"><?= $trainer['designation'] ?></p>
        <div class="trainer-specialization">
          <?php foreach(array_slice($specializations, 0, 3) as $spec): ?>
            <span><?= trim($spec) ?></span>
          <?php endforeach; ?>
          <?php if($trainer['years_experience']): ?>
            <span><?= $trainer['years_experience'] ?>+ Years Exp</span>
          <?php endif; ?>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

  <!-- Success Stories Section -->
  <section id="success" class="success-stories">
    <div class="my-padder"></div>
    <div class="container">
      <h2>Success <span class="gradient-text">Stories</span></h2>
      <p class="section-subtitle">Real people, real transformations from our database</p>
      
      <div class="testimonials-container">
        <?php 
        $counter = 0;
        while($story = $successStories->fetch_assoc()): 
          $counter++;
          $weightLoss = $story['ini_weight'] - $story['curr_weight'];
          $muscleGain = $story['ini_weight'] > $story['curr_weight'] ? round($weightLoss * 0.4) : 0;
          $timePeriod = round((strtotime(date('Y-m-d')) - strtotime($story['progress_date'])) / (60 * 60 * 24 * 30));
        ?>
        <div class="testimonial-card <?= $counter === 1 ? 'active' : '' ?>">
          <div class="testimonial-header">
            <div class="testimonial-image" style="background-image: url('images/members/<?= strtolower(str_replace(' ', '-', $story['fullname'])) ?>.jpg')"></div>
            <div class="testimonial-meta">
              <h3><?= $story['fullname'] ?></h3>
              <div class="testimonial-stats">
                <div class="stat">
                  <span class="label">Lost:</span>
                  <span class="value"><?= $weightLoss ?> lbs</span>
                </div>
                <div class="stat">
                  <span class="label">Gained:</span>
                  <span class="value"><?= $muscleGain ?>% Muscle</span>
                </div>
                <div class="stat">
                  <span class="label">Time:</span>
                  <span class="value"><?= $timePeriod ?> months</span>
                </div>
              </div>
            </div>
          </div>
          <p class="testimonial-text">"EliteFit helped me transform from <?= $story['ini_bodytype'] ?> to <?= $story['curr_bodytype'] ?>. The data-driven approach made all the difference in tracking my progress."</p>
        </div>
        <?php endwhile; ?>
      </div>
      
      <div class="testimonial-dots">
        <span class="dot active"></span>
        <span class="dot"></span>
        <span class="dot"></span>
      </div>
    </div>
  </section>

  <!-- Future Stats Calculator -->
  <section class="future-stats">
    <div class="container">
      <h2>Your Future <span class="gradient-text">Stats</span></h2>
      <p class="section-subtitle">See what's possible with EliteFit based on real member data</p>
      
      <div class="calculator-container">
        <div class="calculator-inputs">
          <div class="input-group">
            <label for="current-weight">Current Weight (lbs)</label>
            <input type="number" id="current-weight" min="80" max="400" value="180">
          </div>
          
          <div class="input-group">
            <label for="goal-type">Primary Goal</label>
            <select id="goal-type">
              <option value="weight-loss">Weight Loss</option>
              <option value="muscle-gain">Muscle Gain</option>
              <option value="endurance">Endurance</option>
              <option value="overall">Overall Fitness</option>
            </select>
          </div>
          
          <div class="input-group">
            <label for="commitment">Weekly Commitment</label>
            <select id="commitment">
              <option value="2">2 Sessions/Week</option>
              <option value="3" selected>3 Sessions/Week</option>
              <option value="4">4 Sessions/Week</option>
              <option value="5">5+ Sessions/Week</option>
            </select>
          </div>
          
          <button id="calculate-btn" class="cta-button">Calculate Results</button>
          <div class="calculator-disclaimer">
            <p>Results are estimates based on statistical data from our <?= $activeMembers ?> member base. Individual results may vary based on genetics, adherence, and other factors. Our technology-driven approach typically produces results 2-3x faster than traditional methods.</p>
          </div>
        </div>
        
        <div class="calculator-results">
          <div class="result-card">
            <h3>3 Months</h3>
            <div class="result-stat" id="three-month-weight">-<?= round($avgWeightLoss * 0.6) ?> lbs</div>
            <div class="result-stat" id="three-month-strength">+<?= round($avgWeightLoss * 0.3) ?>% Strength</div>
            <div class="result-stat" id="three-month-endurance">+<?= round($avgWeightLoss * 0.5) ?>% Endurance</div>
          </div>
          
          <div class="result-card">
            <h3>6 Months</h3>
            <div class="result-stat" id="six-month-weight">-<?= $avgWeightLoss ?> lbs</div>
            <div class="result-stat" id="six-month-strength">+<?= round($avgWeightLoss * 0.6) ?>% Strength</div>
            <div class="result-stat" id="six-month-endurance">+<?= round($avgWeightLoss * 0.9) ?>% Endurance</div>
          </div>
          
          <div class="result-card">
            <h3>12 Months</h3>
            <div class="result-stat" id="twelve-month-weight">-<?= round($avgWeightLoss * 1.8) ?> lbs</div>
            <div class="result-stat" id="twelve-month-strength">+<?= round($avgWeightLoss * 1.2) ?>% Strength</div>
            <div class="result-stat" id="twelve-month-endurance">+<?= round($avgWeightLoss * 1.5) ?>% Endurance</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Next Available Session -->
  <section class="next-session">
    <div class="container">
      <div class="session-card">
        <div class="session-info">
          <h2>Next Available <span class="gradient-text">Session</span></h2>
          <div class="session-time">
            <div class="time-icon">
              <svg viewBox="0 0 24 24" width="24" height="24">
                <path fill="currentColor" d="M12,20A8,8 0 0,0 20,12A8,8 0 0,0 12,4A8,8 0 0,0 4,12A8,8 0 0,0 12,20M12,2A10,10 0 0,1 22,12A10,10 0 0,1 12,22C6.47,22 2,17.5 2,12A10,10 0 0,1 12,2M12.5,7V12.25L17,14.92L16.25,16.15L11,13V7H12.5Z" />
              </svg>
            </div>
            <div class="time-details">
              <div id="next-session-day"><?= date('l', strtotime($nextSession['session_date'])) ?></div>
              <div id="next-session-time"><?= date('g:i A', strtotime($nextSession['session_date'])) ?></div>
            </div>
          </div>
          
          <div class="session-details">
            <div class="detail-item">
              <span class="detail-label">Class:</span>
              <span class="detail-value" id="session-class"><?= $nextSession['workout_name'] ?></span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Trainer:</span>
              <span class="detail-value" id="session-trainer"><?= $nextSession['trainer_name'] ?></span>
            </div>
            <div class="detail-item">
              <span class="detail-label">Spots Left:</span>
              <span class="detail-value" id="session-spots"><?= $spotsLeft ?></span>
            </div>
          </div>
          
          <button class="cta-button primary"><a href="./welcome.php">Reserve Your Spot</a></button>
        </div>
      </div>
    </div>
  </section>

  <!-- Industry Insights Section -->
  <section class="industry-insights">
    <div class="container">
      <h2>Industry <span class="gradient-text">Insights</span></h2>
      <p class="section-subtitle">Stay informed with the latest in fitness science and technology</p>
      
      <div class="insights-grid">
        <div class="insight-card">
          <div class="insight-image" ><img style="aspect-ratio: 1/1; object-fit: cover;" src='https://media.istockphoto.com/id/1556651444/photo/portrait-of-a-smiling-teenage-girl-doing-sports-in-the-city.webp?a=1&b=1&s=612x612&w=0&k=20&c=-oeH9y00Pb7YjEsIHxUTCI9Bq0vdhyf45BRGouWLyUU=' width='100%' height='100%'

            
          
          /></div>
          <div class="insight-content">
            <h3>The Future of Fitness Tracking</h3>
            <p>How our <?= $activeMembers ?> members use wearable technology to optimize their training with biometric insights.</p>
            <a href="#training-optimization" class="insight-link">Read More</a>
          </div>
        </div>
        
        <div class="insight-card">
          <div class="insight-image" ><img style="aspect-ratio: 1/1; object-fit: cover;" src='https://images.unsplash.com/photo-1740560052706-fd75ee856b44?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8Mnx8TnV0cml0aW9uJTIwUGVyaW9kaXphdGlvbnxlbnwwfHwwfHx8MA%3D%3D' width='100%' height='100%'
          
          /></div>
          <div class="insight-content">
            <h3>Nutrition Periodization</h3>
            <p>How <?= round($avgWeightLoss * 1.8) ?> lbs average annual weight loss is achieved through strategic nutrition.</p>
            <a href="#nutrition" class="insight-link">Read More</a>
          </div>
        </div>
        
        <div class="insight-card">
          <div class="insight-image"><img style="aspect-ratio: 1/1; object-fit: cover;" src='https://plus.unsplash.com/premium_photo-1665203632873-0f845413fcf1?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8UmVjb3ZlcnklMjBTY2llbmNlfGVufDB8fDB8fHww' width='100%' height='100%'
          
          /></div>
          <div class="insight-content">
            <h3>Recovery Science</h3>
            <p>Why our members who focus on recovery see <?= round($avgWeightLoss * 1.2) ?>% better results than those who don't.</p>
            <a href="#recovery-science" class="insight-link">Read More</a>
          </div>
        </div>
        
        <div class="insight-card">
          <div class="insight-image" ><img  style="aspect-ratio: 1/1; object-fit: cover;" src='https://images.unsplash.com/photo-1716367840407-f9414a84b325?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTR8fEFJJTIwaW4lMjBGaXRuZXNzJTIwUHJvZ3JhbW1pbmd8ZW58MHx8MHx8fDA%3D' width='100%' height='100%'
          
          /></div>
          <div class="insight-content">
            <h3>AI in Fitness Programming</h3>
            <p>How our <?= $weeklySessions ?> weekly sessions are optimized using machine learning algorithms.</p>
            <a href="#ai-fitness" class="insight-link">Read More</a>
          </div>
        </div>
      </div>
      
      <div class="industry-stats">
        <div class="stat-item">
          <div class="stat-number"><?= round(($activeMembers / ($activeMembers + 50)) * 100) ?>%</div>
          <div class="stat-description">of our members use technology to track their progress</div>
        </div>
        <div class="stat-item">
          <div class="stat-number"><?= round($avgWeightLoss / 10, 1) ?>x</div>
          <div class="stat-description">faster results with our data-driven training</div>
        </div>
        <div class="stat-item">
          <div class="stat-number">87%</div>
          <div class="stat-description">higher adherence rate with personalized programming</div>
        </div>
        <div class="stat-item">
          <div class="stat-number"><?= round($activeMembers * 1200) ?></div>
          <div class="stat-description">total calories burned by our members daily</div>
        </div>
      </div>
    </div>
   <!-- Modals -->
<div class="modal" id="training-optimization">
  <div class="modal-content" style="background: #f9f9f9; border-radius: 12px; max-width: 800px; margin: 2rem auto; padding: 2.5rem; position: relative;">
    <span class="close" style="position: absolute; right: 25px; top: 15px; font-size: 32px; cursor: pointer; color: #444;">&times;</span>
    <div style="max-width: 700px; margin: 0 auto;">
      <h3 style="font-size: 28px; color: #2a2a2a; margin-bottom: 1.5rem; border-bottom: 2px solid #4CAF50; padding-bottom: 0.5rem;">
        The Future of Fitness Tracking
      </h3>
      <div style="display: flex; gap: 2rem; margin-bottom: 2rem; align-items: center;">
        <div style="flex: 1;">
          <p style="color: #555; line-height: 1.7; margin-bottom: 1.5rem; font-size: 16px;">
            With <?= $activeMembers ?> active members leveraging cutting-edge wearable technology, our platform has revolutionized...
          </p>
          <div style="background: #e8f5e9; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <h4 style="color: #000; margin-bottom: 1rem; font-size: 20px;">Key Benefits:</h4>
            <ul style="list-style: none; padding-left: 0;">
              <li style="margin-bottom: 0.8rem; padding-left: 1.5rem; position: relative; color:#2c3640;">
                <span style="position: absolute; left: 0; color: #000;">✓</span>
                Real-time heart rate variability tracking
              </li>
              <!-- Add more list items -->
            </ul>
          </div>
        </div>
      </div>
      <button style="background: #4CAF50; color: white; padding: 12px 30px; border: none; border-radius: 25px; font-size: 16px; cursor: pointer; transition: all 0.3s ease;" 
              onmouseover="this.style.backgroundColor='#45a049'" 
              onmouseout="this.style.backgroundColor='#4CAF50'">
        <a href="./welcome.php">Start Tracking Now </a>
      </button>
    </div>
  </div>
</div>

<div class="modal" id="nutrition">
  <div class="modal-content" style="background: #fff; border-radius: 12px; max-width: 800px; margin: 2rem auto; padding: 2.5rem; position: relative;">
    <span class="close" style="position: absolute; right: 25px; top: 15px; font-size: 32px; cursor: pointer; color: #444;">&times;</span>
    <div style="max-width: 700px; margin: 0 auto;">
      <h3 style="font-size: 28px; color: #2a2a2a; margin-bottom: 1.5rem; border-bottom: 2px solid #ff9800; padding-bottom: 0.5rem;">
        Nutrition Periodization
      </h3>
      <div style="margin-bottom: 2rem;">
        <p style="font-size: 18px; color: #666; line-height: 1.7; margin-bottom: 1.5rem;">
          Our members achieve an average of <?= round($avgWeightLoss * 1.8) ?> lbs annual weight loss through...
        </p>
        <div style="display: grid; grid-template-columns: 1fr ; gap: 1.5rem; margin-bottom: 2rem;">
          <div style="background: #fff3e0; padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #ef6c00; margin-bottom: 1rem;">Macro Cycling</h4>
            <p style="color: #666; font-size: 15px;">Strategic carbohydrate rotation.</p>
            <p style="color: #666; font-size: 15px;">Increase in protein intake. </p>
          </div>
          <!-- Add more grid items -->
        </div>
      </div>
      <button style="background: #ff9800; color: white; padding: 12px 30px; border: none; border-radius: 25px; font-size: 16px; cursor: pointer; transition: all 0.3s ease;" 
              onmouseover="this.style.backgroundColor='#f57c00'" 
              onmouseout="this.style.backgroundColor='#ff9800'">
        <a href="./welcome.php">Nutrition Plan </a>
      </button>
    </div>
  </div>
</div>
<div class="modal" id="recovery-science">
  <div class="modal-content" style="background: #fff; border-radius: 12px; max-width: 800px; margin: 2rem auto; padding: 2.5rem; position: relative;">
    <span class="close" style="position: absolute; right: 25px; top: 15px; font-size: 32px; cursor: pointer; color: #444;">&times;</span>
    <div style="max-width: 700px; margin: 0 auto;">
      <h3 style="font-size: 28px; color: #2a2a2a; margin-bottom: 1.5rem; border-bottom: 2px solid #ff9800; padding-bottom: 0.5rem;">
        Recovery Science
      </h3>
      <div style="margin-bottom: 2rem;">
        <p style="font-size: 18px; color: #666; line-height: 1.7; margin-bottom: 1.5rem;">
          Our members achieve an average of <?= round($avgWeightLoss * 1.8) ?> lbs annual weight loss through...
        </p>
        <div style="display: grid; grid-template-columns: 1fr ; gap: 1.5rem; margin-bottom: 2rem;">
          <div style="background: #fff6e0; padding: 1.5rem; border-radius: 8px;">
            <h4 style="color: #ef6c00; margin-bottom: 1rem;">Rest as an Excercise</h4>
            <p style="color: #666; font-size: 15px;">Periodic rest excercises</p>
            <p style="color: #666; font-size: 15px;">Activities to bring the nerves down and ensure that muscles get time to recover </p>
          </div>
          <!-- Add more grid items -->
        </div>
      </div>
      <button style="background: green; color: white; padding: 12px 30px; border: none; border-radius: 25px; font-size: 16px; cursor: pointer; transition: all 0.3s ease;" 
              onmouseover="this.style.backgroundColor='#f57c00'" 
              onmouseout="this.style.backgroundColor='#ff9800'">
        <a href="./welcome.php">Join Us Today</a>
      </button>
    </div>
  </div>
</div>
<div class="modal" id="ai-fitness">
  <div class="modal-content" style="background: #fff; border-radius: 12px; max-width: 800px; margin: 2rem auto; padding: 2.5rem; position: relative;">
    <span class="close" style="position: absolute; right: 25px; top: 15px; font-size: 32px; cursor: pointer; color: #444;">&times;</span>
    <div style="max-width: 700px; margin: 0 auto;">
      <h3 style="font-size: 28px; color: #2a2a2a; margin-bottom: 1.5rem; border-bottom: 2px solid #ff9800; padding-bottom: 0.5rem;">
        AI in Fitness Programming
      </h3>
      <div style="margin-bottom: 2rem;">
        <p style="font-size: 18px; color: #666; line-height: 1.7; margin-bottom: 1.5rem;">
          The simple idea of the organization that AI and systematic programming bring to the scope of 'working' out is impeccable
        </p>
        <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
          <div style="background: #fff3e0; padding: 1.5rem; border-radius: 8px; ">
            <h4 style="color: #ef6c00; margin-bottom: 1rem;">Systematic Scheduling</h4>
            <p style="color: #666; font-size: 15px;">Have all your tasks regulated by AI to gain maximum results</p>
          </div>
          <!-- <div style="background: #fff3e0; padding: 1.5rem; border-radius: 8px; width:100%">
            <h4 style="color: #ef6c00; margin-bottom: 1rem;">Systematic Scheduling</h4>
            <p style="color: #666; font-size: 15px;">Have all your tasks regulated by AI to gain maximum results</p>
          </div> -->
          <!-- Add more grid items -->
        </div>
      </div>
      <button style="background: green; color: white; padding: 12px 30px; border: none; border-radius: 25px; font-size: 16px; cursor: pointer; transition: all 0.3s ease;" 
              onmouseover="this.style.backgroundColor='#f57c00'" 
              onmouseout="this.style.backgroundColor='#ff9800'">
        <a href="./welcome.php">Register Now </a>
      </button>
    </div>
  </div>
</div>

  </section>



  <!-- Contact Section -->
<section id="contact" class="contact-section">
  <div class="my-padder"></div>
  <div class="container">
    <h2>Get in <span class="gradient-text">Touch</span></h2>
    <p class="section-subtitle">We're here to help you achieve your fitness goals</p>

    <div class="contact-grid">
      <!-- Contact Cards -->
      <div class="contact-card">
        <div class="contact-icon" style="background: #4CAF50;">
          <i class="fas fa-envelope"></i>
        </div>
        <h3>Email Us</h3>
        <p>Questions or feedback? We respond within 24 hours</p>
        <a href="mailto:fitness@gmail.com" class="contact-btn" style="background: #4CAF50;">
          fitness@gmail.com
        </a>
      </div>

      <div class="contact-card">
        <div class="contact-icon" style="background: #2196F3;">
          <i class="fas fa-phone"></i>
        </div>
        <h3>Call Us</h3>
        <p>Speak directly with our fitness experts. 
          <!-- bootstrap info icon -->
          <i class="fas fa-info-circle" style="color: #2196F3;"></i>
        info</p>
        <a href="tel:+233123456789" class="contact-btn" style="background: #2196F3;">
          +233 123 456 789
        </a>
      </div>

      <div class="contact-card">
        <div class="contact-icon" style="background: #ff9800;">
          <i class="fas fa-map-marker-alt"></i>
        </div>
        <h3>Visit Us</h3>
        <p>Our flagship location in Accra</p>
        <a href="https://goo.gl/maps/abc123xyz" target="_blank" class="contact-btn" style="background: #ff9800;">
          Open in Maps
        </a>
      </div>
    </div>

    <!-- Embedded Google Map -->
    <div class="map-container">
      <iframe 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3970.755653715719!2d-0.181069685556683!3d5.606686295932724!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9084b2b85193%3A0x34a02905a6df7e0e!2sThe%20Junction%20Mall!5e0!3m2!1sen!2sgh!4v1623436789018!5m2!1sen!2sgh" 
        width="100%" 
        height="450" 
        style="border:0;" 
        allowfullscreen="" 
        loading="lazy"
        aria-label="Our Location in Accra">
      </iframe>
    </div>
  </div>
</section>


  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="footer-content">
        <div class="footer-logo">EliteFit</div>
        <div class="footer-links">
          <div class="footer-column">
            <h4>Company</h4>
            <a href="about.html">About Us</a>
            <a href="careers.html">Careers</a>
            <a href="press.html">Press</a>
          </div>
          <div class="footer-column">
            <h4>Resources</h4>
            <a href="https://medium.com/tag/fitness" >Blog</a>
            <a href="https://medium.com/@drjasonfung/control-hunger-not-calories-95c9076710f0">Nutrition Guide</a>
            <a href="https://medium.com/tag/workout-routines">Workout Library</a>
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
            <a
              href="https://instagram.com/elitefit"
              target="_blank"
              class="social-icon"
            >
              <svg viewBox="0 0 24 24" width="24" height="24">
                <path
                  fill="currentColor"
                  d="M7.8,2H16.2C19.4,2 22,4.6 22,7.8V16.2A5.8,5.8 0 0,1 16.2,22H7.8C4.6,22 2,19.4 2,16.2V7.8A5.8,5.8 0 0,1 7.8,2M7.6,4A3.6,3.6 0 0,0 4,7.6V16.4C4,18.39 5.61,20 7.6,20H16.4A3.6,3.6 0 0,0 20,16.4V7.6C20,5.61 18.39,4 16.4,4H7.6M17.25,5.5A1.25,1.25 0 0,1 18.5,6.75A1.25,1.25 0 0,1 17.25,8A1.25,1.25 0 0,1 16,6.75A1.25,1.25 0 0,1 17.25,5.5M12,7A5,5 0 0,1 17,12A5,5 0 0,1 12,17A5,5 0 0,1 7,12A5,5 0 0,1 12,7M12,9A3,3 0 0,0 9,12A3,3 0 0,0 12,15A3,3 0 0,0 15,12A3,3 0 0,0 12,9Z"
                />
              </svg>
            </a>
            <a
              href="https://x.com/elitefit"
              target="_blank"
              class="social-icon"
            >
              <svg viewBox="0 0 24 24" width="24" height="24">
                <path
                  fill="currentColor"
                  d="M22.46,6C21.69,6.35 20.86,6.58 20,6.69C20.88,6.16 21.56,5.32 21.88,4.31C21.05,4.81 20.13,5.16 19.16,5.36C18.37,4.5 17.26,4 16,4C13.65,4 11.73,5.92 11.73,8.29C11.73,8.63 11.77,8.96 11.84,9.27C8.28,9.09 5.11,7.38 3,4.79C2.63,5.42 2.42,6.16 2.42,6.94C2.42,8.43 3.17,9.75 4.33,10.5C3.62,10.5 2.96,10.3 2.38,10C2.38,10 2.38,10 2.38,10.03C2.38,12.11 3.86,13.85 5.82,14.24C5.46,14.34 5.08,14.39 4.69,14.39C4.42,14.39 4.15,14.36 3.89,14.31C4.43,16 6,17.26 7.89,17.29C6.43,18.45 4.58,19.13 2.56,19.13C2.22,19.13 1.88,19.11 1.54,19.07C3.44,20.29 5.7,21 8.12,21C16,21 20.33,14.46 20.33,8.79C20.33,8.6 20.33,8.42 20.32,8.23C21.16,7.63 21.88,6.87 22.46,6Z"
                />
              </svg>
            </a>
            <a
              href="https://linkedin.com/elitefit"
              target="_blank"
              class="social-icon"
            >
              <svg viewBox="0 0 24 24" width="24" height="24">
                <path
                  fill="currentColor"
                  d="M19,3A2,2 0 0,1 21,5V19A2,2 0 0,1 19,21H5A2,2 0 0,1 3,19V5A2,2 0 0,1 5,3H19M18.5,18.5V13.2A3.26,3.26 0 0,0 15.24,9.94C14.39,9.94 13.4,10.46 12.92,11.24V10.13H10.13V18.5H12.92V13.57C12.92,12.8 13.54,12.17 14.31,12.17A1.4,1.4 0 0,1 15.71,13.57V18.5H18.5M6.88,8.56A1.68,1.68 0 0,0 8.56,6.88C8.56,5.95 7.81,5.19 6.88,5.19A1.69,1.69 0 0,0 5.19,6.88C5.19,7.81 5.95,8.56 6.88,8.56M8.27,18.5V10.13H5.5V18.5H8.27Z"
                />
              </svg>
            </a>
          </div>
</div>
</div>

</footer> <script src="script.js"></script> <script> 
// Dynamic calculator functionality 
document.getElementById('calculate-btn').addEventListener('click', function() { const currentWeight = parseFloat(document.getElementById('current-weight').value); const goalType = document.getElementById('goal-type').value; const commitment = parseInt(document.getElementById('commitment').value);
   // Calculate results based on database averages and user input 
   const weightLoss3Months = <?= round($avgWeightLoss * 0.6) ?> * (commitment / 3); const weightLoss6Months = <?= $avgWeightLoss ?> * (commitment / 3); const weightLoss12Months = <?= round($avgWeightLoss * 1.8) ?> * (commitment / 3);
    // Strength and endurance gains are percentage of weight loss 
    const strengthGain3Months = <?= round($avgWeightLoss * 0.3) ?> * (commitment / 3); const strengthGain6Months = <?= round($avgWeightLoss * 0.6) ?> * (commitment / 3); const strengthGain12Months = <?= round($avgWeightLoss * 1.2) ?> * (commitment / 3); const enduranceGain3Months = <?= round($avgWeightLoss * 0.5) ?> * (commitment / 3); const enduranceGain6Months = <?= round($avgWeightLoss * 0.9) ?> * (commitment / 3); const enduranceGain12Months = <?= round($avgWeightLoss * 1.5) ?> * (commitment / 3);
    
     // Adjust for goal type 
     let weightModifier = 1; let strengthModifier = 1; let enduranceModifier = 1; switch(goalType) { case 'weight-loss': weightModifier = 1.2; strengthModifier = 0.8; enduranceModifier = 1; break; case 'muscle-gain': weightModifier = 0.5; strengthModifier = 1.5; enduranceModifier = 0.7; break; case 'endurance': weightModifier = 0.8; strengthModifier = 0.7; enduranceModifier = 1.5; break; case 'overall': weightModifier = 1; strengthModifier = 1; enduranceModifier = 1; break; } ;
     // Update results
      document.getElementById('three-month-weight').textContent = `-${Math.round(weightLoss3Months * weightModifier)} lbs`; document.getElementById('three-month-strength').textContent = `+${Math.round(strengthGain3Months * strengthModifier)}% Strength`; document.getElementById('three-month-endurance').textContent = `+${Math.round(enduranceGain3Months * enduranceModifier)}% Endurance`; document.getElementById('six-month-weight').textContent = `-${Math.round(weightLoss6Months * weightModifier)} lbs`; document.getElementById('six-month-strength').textContent = `+${Math.round(strengthGain6Months * strengthModifier)}% Strength`; document.getElementById('six-month-endurance').textContent = `+${Math.round(enduranceGain6Months * enduranceModifier)}% Endurance`; document.getElementById('twelve-month-weight').textContent = `-${Math.round(weightLoss12Months * weightModifier)} lbs`; document.getElementById('twelve-month-strength').textContent = `+${Math.round(strengthGain12Months * strengthModifier)}% Strength`; document.getElementById('twelve-month-endurance').textContent = `+${Math.round(enduranceGain12Months * enduranceModifier)}% Endurance`; }); 
      </script>
      <!-- Add this JavaScript -->
<script>
document.querySelector('.menu-toggle').addEventListener('click', function() {
    this.classList.toggle('active');
    document.querySelector('.nav-links').classList.toggle('active');
});

// Close menu when clicking outside or on a link
document.addEventListener('click', function(event) {
    const navLinks = document.querySelector('.nav-links');
    const menuToggle = document.querySelector('.menu-toggle');
    
    if (!event.target.closest('.nav-container') && navLinks.classList.contains('active')) {
        menuToggle.classList.remove('active');
        navLinks.classList.remove('active');
    }
});

document.querySelectorAll('.nav-links a').forEach(link => {
    link.addEventListener('click', () => {
        document.querySelector('.menu-toggle').classList.remove('active');
        document.querySelector('.nav-links').classList.remove('active');
    });
});
</script>
      </body> 
      </html> 