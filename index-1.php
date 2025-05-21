<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EliteFit - Gym Management System</title>
  <meta name="description" content="A comprehensive gym management system for tracking members, equipment, trainers, and more.">
  <link rel="stylesheet" href="styles.css">
  <!-- Font import -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <header class="header">
    <div class="container header-container">
      <div class="logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
          <path d="M6.5 6.5h11"></path>
          <path d="M6.5 17.5h11"></path>
          <path d="M3 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
          <path d="M3 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
          <path d="M21 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
          <path d="M21 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
        </svg>
        <span>EliteFit</span>
      </div>
      <nav class="nav">
        <a href="#features" class="nav-link">Features</a>
        <a href="#testimonials" class="nav-link">Testimonials</a>
        <a href="#pricing" class="nav-link">Pricing</a>
        <a href="#about" class="nav-link">About</a>
      </nav>
      <div class="header-buttons">
        <button class="btn btn-outline">Log In</button>
        <button class="btn btn-primary">Sign Up</button>
      </div>
      <button class="mobile-menu-toggle" aria-label="Toggle menu">
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </header>

  <main>
    <!-- Hero Section -->
    <section class="hero">
      <div class="container">
        <div class="hero-grid">
          <div class="hero-content">
            <h1 class="hero-title">Manage Your Gym with Confidence</h1>
            <p class="hero-description">
              EliteFit is a comprehensive gym management system that helps you track members, equipment, trainers, and more.
            </p>
            <div class="hero-buttons">
              <button class="btn btn-primary btn-lg">
                Get Started
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                  <path d="M5 12h14"></path>
                  <path d="m12 5 7 7-7 7"></path>
                </svg>
              </button>
              <button class="btn btn-outline btn-lg">View Demo</button>
            </div>
          </div>
          <div class="hero-image">
            <div class="hero-image-content">
              <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M6.5 6.5h11"></path>
                <path d="M6.5 17.5h11"></path>
                <path d="M3 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M3 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
              </svg>
              <h3>EliteFit Dashboard</h3>
              <p>Powerful gym management at your fingertips</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-card">
            <div class="stat-header">
              <h3 class="stat-title">Active Members</h3>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value">42</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-header">
              <h3 class="stat-title">Trainers</h3>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M12 8a2.83 2.83 0 0 0 4 4 4 4 0 1 1-4-4Z"></path>
                <path d="M12 2v2"></path>
                <path d="M12 20v2"></path>
                <path d="m4.9 4.9 1.4 1.4"></path>
                <path d="m17.7 17.7 1.4 1.4"></path>
                <path d="M2 12h2"></path>
                <path d="M20 12h2"></path>
                <path d="m6.3 17.7-1.4 1.4"></path>
                <path d="m19.1 4.9-1.4 1.4"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value">7</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-header">
              <h3 class="stat-title">Equipment Items</h3>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M6.5 6.5h11"></path>
                <path d="M6.5 17.5h11"></path>
                <path d="M3 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M3 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value">11</div>
            </div>
          </div>
          <div class="stat-card">
            <div class="stat-header">
              <h3 class="stat-title">Workout Plans</h3>
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                <line x1="16" x2="16" y1="2" y2="6"></line>
                <line x1="8" x2="8" y1="2" y2="6"></line>
                <line x1="3" x2="21" y1="10" y2="10"></line>
              </svg>
            </div>
            <div class="stat-content">
              <div class="stat-value">16</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
      <div class="container">
        <div class="section-header">
          <div class="badge">Features</div>
          <h2 class="section-title">Everything You Need</h2>
          <p class="section-description">
            EliteFit provides a comprehensive set of tools to manage every aspect of your gym.
          </p>
        </div>
        <div class="features-grid">
          <div class="feature-card">
            <div class="feature-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
              </svg>
            </div>
            <h3 class="feature-title">Member Management</h3>
            <p class="feature-description">
              Track member profiles, attendance, fitness goals, and progress over time.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                <line x1="16" x2="16" y1="2" y2="6"></line>
                <line x1="8" x2="8" y1="2" y2="6"></line>
                <line x1="3" x2="21" y1="10" y2="10"></line>
              </svg>
            </div>
            <h3 class="feature-title">Trainer Scheduling</h3>
            <p class="feature-description">
              Manage trainer schedules, specializations, and client sessions.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M6.5 6.5h11"></path>
                <path d="M6.5 17.5h11"></path>
                <path d="M3 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M3 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
              </svg>
            </div>
            <h3 class="feature-title">Equipment Tracking</h3>
            <p class="feature-description">
              Monitor gym equipment inventory, status, and maintenance needs.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                <line x1="2" x2="22" y1="10" y2="10"></line>
              </svg>
            </div>
            <h3 class="feature-title">Payment Processing</h3>
            <p class="feature-description">
              Handle membership fees, track transactions, and manage payment logs.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M12 8a2.83 2.83 0 0 0 4 4 4 4 0 1 1-4-4Z"></path>
                <path d="M12 2v2"></path>
                <path d="M12 20v2"></path>
                <path d="m4.9 4.9 1.4 1.4"></path>
                <path d="m17.7 17.7 1.4 1.4"></path>
                <path d="M2 12h2"></path>
                <path d="M20 12h2"></path>
                <path d="m6.3 17.7-1.4 1.4"></path>
                <path d="m19.1 4.9-1.4 1.4"></path>
              </svg>
            </div>
            <h3 class="feature-title">Workout Planning</h3>
            <p class="feature-description">
              Create and assign customized workout plans based on member goals.
            </p>
          </div>
          <div class="feature-card">
            <div class="feature-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M3 3v18h18"></path>
                <path d="m19 9-5 5-4-4-3 3"></path>
              </svg>
            </div>
            <h3 class="feature-title">Performance Analytics</h3>
            <p class="feature-description">
              Generate reports on member progress, attendance, and financial metrics.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials">
      <div class="container">
        <div class="section-header">
          <div class="badge">Testimonials</div>
          <h2 class="section-title">What Our Users Say</h2>
          <p class="section-description">
            Don't just take our word for it. Here's what gym owners and members have to say about EliteFit.
          </p>
        </div>
        <div class="testimonials-grid">
          <div class="testimonial-card">
            <div class="testimonial-content">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path>
                <path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path>
              </svg>
              <p class="testimonial-quote">
                EliteFit has transformed how we manage our gym. The member tracking and workout planning features are exceptional.
              </p>
            </div>
            <div class="testimonial-footer">
              <div class="testimonial-author">Harry Denn</div>
              <div class="testimonial-role">Fitness Member since 2019</div>
            </div>
          </div>
          <div class="testimonial-card">
            <div class="testimonial-content">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path>
                <path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path>
              </svg>
              <p class="testimonial-quote">
                The trainer scheduling system has made it so much easier to organize my sessions with clients.
              </p>
            </div>
            <div class="testimonial-footer">
              <div class="testimonial-author">Michelle R. Lane</div>
              <div class="testimonial-role">Trainer</div>
            </div>
          </div>
          <div class="testimonial-card">
            <div class="testimonial-content">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path>
                <path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2h.75c0 2.25.25 4-2.75 4v3c0 1 0 1 1 1z"></path>
              </svg>
              <p class="testimonial-quote">
                I love being able to track my progress and see my fitness journey over time.
              </p>
            </div>
            <div class="testimonial-footer">
              <div class="testimonial-author">Charles Anderson</div>
              <div class="testimonial-role">Fitness Member</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="pricing">
      <div class="container">
        <div class="section-header">
          <div class="badge">Pricing</div>
          <h2 class="section-title">Membership Plans</h2>
          <p class="section-description">
            Choose the plan that works best for your fitness goals.
          </p>
        </div>
        <div class="pricing-grid">
          <div class="pricing-card">
            <div class="pricing-header">
              <h3 class="pricing-title">Fitness</h3>
              <p class="pricing-subtitle">Membership Plan</p>
            </div>
            <div class="pricing-content">
              <div class="pricing-price">
                <span class="price">$55</span>
                <span class="duration">/monthly</span>
              </div>
              <ul class="pricing-features">
                <li class="pricing-feature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M20 6 9 17l-5-5"></path>
                  </svg>
                  <span>Full gym access</span>
                </li>
                <li class="pricing-feature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M20 6 9 17l-5-5"></path>
                  </svg>
                  <span>Personalized workout plans</span>
                </li>
                <li class="pricing-feature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M20 6 9 17l-5-5"></path>
                  </svg>
                  <span>Progress tracking</span>
                </li>
              </ul>
            </div>
            <div class="pricing-footer">
              <button class="btn btn-primary btn-block">Get Started</button>
            </div>
          </div>
          <div class="pricing-card popular">
            <div class="pricing-popular-badge">Most Popular</div>
            <div class="pricing-header">
              <h3 class="pricing-title">Cardio</h3>
              <p class="pricing-subtitle">Membership Plan</p>
            </div>
            <div class="pricing-content">
              <div class="pricing-price">
                <span class="price">$40</span>
                <span class="duration">/monthly</span>
              </div>
              <ul class="pricing-features">
                <li class="pricing-feature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M20 6 9 17l-5-5"></path>
                  </svg>
                  <span>Cardio equipment access</span>
                </li>
                <li class="pricing-feature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M20 6 9 17l-5-5"></path>
                  </svg>
                  <span>Heart rate monitoring</span>
                </li>
                <li class="pricing-feature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M20 6 9 17l-5-5"></path>
                  </svg>
                  <span>Endurance training</span>
                </li>
              </ul>
            </div>
            <div class="pricing-footer">
              <button class="btn btn-primary btn-block">Get Started</button>
            </div>
          </div>
          <div class="pricing-card">
            <div class="pricing-header">
              <h3 class="pricing-title">Sauna</h3>
              <p class="pricing-subtitle">Membership Plan</p>
            </div>
            <div class="pricing-content">
              <div class="pricing-price">
                <span class="price">$35</span>
                <span class="duration">/monthly</span>
              </div>
              <ul class="pricing-features">
                <li class="pricing-feature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M20 6 9 17l-5-5"></path>
                  </svg>
                  <span>Sauna access</span>
                </li>
                <li class="pricing-feature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M20 6 9 17l-5-5"></path>
                  </svg>
                  <span>Relaxation amenities</span>
                </li>
                <li class="pricing-feature">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                    <path d="M20 6 9 17l-5-5"></path>
                  </svg>
                  <span>Recovery sessions</span>
                </li>
              </ul>
            </div>
            <div class="pricing-footer">
              <button class="btn btn-primary btn-block">Get Started</button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
      <div class="container">
        <div class="about-grid">
          <div class="about-content">
            <div class="badge">About</div>
            <h2 class="about-title">About EliteFit</h2>
            <p class="about-description">
              EliteFit is a comprehensive gym management system designed to streamline operations for fitness centers of all sizes.
            </p>
            <div class="about-text">
              <p>
                Our system helps you manage members, track attendance, schedule training sessions, maintain equipment inventory, process payments, and much more. With EliteFit, you can focus on what matters most - helping your members achieve their fitness goals.
              </p>
              <p>
                Founded in 2023, EliteFit has quickly become the preferred management solution for forward-thinking gyms and fitness centers.
              </p>
            </div>
          </div>
          <div class="about-image">
            <div class="about-image-content">
              <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
                <path d="M6.5 6.5h11"></path>
                <path d="M6.5 17.5h11"></path>
                <path d="M3 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M3 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
                <path d="M21 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
              </svg>
              <h3>Our Mission</h3>
              <p>To empower fitness businesses with powerful, easy-to-use management tools</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer">
    <div class="container">
      <div class="footer-content">
        <div class="footer-logo">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon">
            <path d="M6.5 6.5h11"></path>
            <path d="M6.5 17.5h11"></path>
            <path d="M3 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
            <path d="M3 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
            <path d="M21 10a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
            <path d="M21 19a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5Z"></path>
          </svg>
          <span>EliteFit</span>
        </div>
        <p class="footer-copyright">&copy; <span id="current-year"></span> EliteFit. All rights reserved.</p>
        <div class="footer-links">
          <a href="#" class="footer-link">Terms</a>
          <a href="#" class="footer-link">Privacy</a>
          <a href="#" class="footer-link">Contact</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="script.js"></script>
</body>
</html>
