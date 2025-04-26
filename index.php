<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="EliteFit - Premium fitness center offering personalized training programs, state-of-the-art equipment, and expert coaching.">
    <title>EliteFit - Premium Fitness Center</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="https://via.placeholder.com/40" alt="EliteFit Logo" class="me-2">
                EliteFit
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#membership">Membership</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#trainers">Trainers</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-primary sharp" href="./gym-system/index.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header id="home" class="hero py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <h1 class="display-4 fw-bold mb-4">Fitness for Everyone, <span class="d-block">Excellence for <strong>you</strong></span></h1>
                    <p class="lead mb-4">Experience premium fitness with state-of-the-art equipment, expert trainers, and personalized programs designed to help you achieve your fitness goals.
                        <i class="d-block mt-2">EliteFit isn't just a gym — it's a lifestyle revolution.</i>
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        <a href="register.html" class="btn btn-primary btn-lg sharp">Get Started</a>
                        <a href="#membership" class="btn btn-outline-primary btn-lg sharp">View Plans</a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Features Section -->
    <section class="features py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100 p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-dumbbell fa-2x"></i>
                        </div>
                        <h3 class="h4 mb-3">Modern Equipment</h3>
                        <p class="text-muted">Access to premium, state-of-the-art fitness equipment for all your training needs.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100 p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                        <h3 class="h4 mb-3">Expert Trainers</h3>
                        <p class="text-muted">Work with certified fitness professionals who will guide your fitness journey.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100 p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-calendar-check fa-2x"></i>
                        </div>
                        <h3 class="h4 mb-3">Flexible Classes</h3>
                        <p class="text-muted">Choose from a variety of classes that fit your schedule and fitness goals.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="feature-card h-100 p-4 text-center">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-heart fa-2x"></i>
                        </div>
                        <h3 class="h4 mb-3">Wellness Programs</h3>
                        <p class="text-muted">Comprehensive wellness programs focusing on nutrition, recovery, and mental health.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">About EliteFit</h2>
                <p class="text-muted"><i>"Where fitness meets excellence"</i></p>
            </div>
            <div class="row align-items-center g-5">
                <div class="col-lg-6">
                    <img src="https://via.placeholder.com/600x500" alt="EliteFit Gym Interior" class="img-fluid sharp">
                </div>
                <div class="col-lg-6">
                    <h3 class="h2 mb-4">Your Premium Fitness Destination</h3>
                    <p class="mb-3">Founded in 2015, EliteFit has grown to become the leading fitness center focused on delivering exceptional results through personalized training and premium facilities.</p>
                    <p class="mb-4">Our mission is to empower individuals to achieve their fitness goals in a supportive, motivating environment with access to the best equipment and expert guidance.</p>
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check text-primary me-2"></i>
                                <span>24/7 Access</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check text-primary me-2"></i>
                                <span>Personal Training</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check text-primary me-2"></i>
                                <span>Nutrition Counseling</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check text-primary me-2"></i>
                                <span>Group Classes</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check text-primary me-2"></i>
                                <span>Recovery Zone</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-check text-primary me-2"></i>
                                <span>Mobile App</span>
                            </div>
                        </div>
                    </div>
                    <a href="#contact" class="btn btn-primary sharp">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Our Services</h2>
                <p class="text-muted"><i>"Comprehensive fitness solutions for everyone"</i></p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100 sharp">
                        <img src="https://via.placeholder.com/400x300" alt="Personal Training" class="img-fluid">
                        <div class="service-content p-4">
                            <h3 class="h4 mb-3">Personal Training</h3>
                            <p class="text-muted mb-3">One-on-one sessions with certified trainers tailored to your specific goals and fitness level.</p>
                            <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100 sharp">
                        <img src="https://via.placeholder.com/400x300" alt="Group Classes" class="img-fluid">
                        <div class="service-content p-4">
                            <h3 class="h4 mb-3">Group Classes</h3>
                            <p class="text-muted mb-3">Energetic, instructor-led classes including HIIT, yoga, cycling, and more for all fitness levels.</p>
                            <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100 sharp">
                        <img src="https://via.placeholder.com/400x300" alt="Nutrition Coaching" class="img-fluid">
                        <div class="service-content p-4">
                            <h3 class="h4 mb-3">Nutrition Coaching</h3>
                            <p class="text-muted mb-3">Expert guidance on nutrition to complement your fitness routine and maximize results.</p>
                            <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="service-card h-100 sharp">
                        <img src="https://via.placeholder.com/400x300" alt="Recovery Services" class="img-fluid">
                        <div class="service-content p-4">
                            <h3 class="h4 mb-3">Recovery Services</h3>
                            <p class="text-muted mb-3">Specialized recovery treatments including massage therapy, stretching, and cryotherapy.</p>
                            <a href="#" class="service-link">Learn More <i class="fas fa-arrow-right ms-1"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Membership Section -->
    <section id="membership" class="membership py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Membership Plans</h2>
                <p class="text-muted"><i>"Find the <b>perfect plan</b> for your fitness journey"</i></p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="pricing-card h-100 sharp">
                        <div class="pricing-header p-4 text-center border-bottom">
                            <h3 class="h3 mb-3">Basic</h3>
                            <div class="price mb-0">
                                <span class="currency">$</span>
                                <span class="amount">49</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <div class="pricing-features p-4">
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>Gym Access (6AM-10PM)</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>Basic Equipment</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>2 Group Classes/Month</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>Fitness Assessment</span>
                                </li>
                                <li class="d-flex align-items-center mb-3 text-muted">
                                    <i class="fas fa-times me-2"></i>
                                    <span>Personal Training</span>
                                </li>
                                <li class="d-flex align-items-center mb-3 text-muted">
                                    <i class="fas fa-times me-2"></i>
                                    <span>Nutrition Coaching</span>
                                </li>
                            </ul>
                        </div>
                        <div class="pricing-footer p-4 text-center">
                            <a href="register.html" class="btn btn-outline-primary w-100 sharp">Choose Plan</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pricing-card featured h-100 sharp">
                        <div class="pricing-badge">Popular</div>
                        <div class="pricing-header p-4 text-center border-bottom">
                            <h3 class="h3 mb-3">Premium</h3>
                            <div class="price mb-0">
                                <span class="currency">$</span>
                                <span class="amount">89</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <div class="pricing-features p-4">
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>24/7 Gym Access</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>All Equipment</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>Unlimited Group Classes</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>Advanced Fitness Assessment</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>2 Personal Training Sessions/Month</span>
                                </li>
                                <li class="d-flex align-items-center mb-3 text-muted">
                                    <i class="fas fa-times me-2"></i>
                                    <span>Nutrition Coaching</span>
                                </li>
                            </ul>
                        </div>
                        <div class="pricing-footer p-4 text-center">
                            <a href="register.html" class="btn btn-primary w-100 sharp">Choose Plan</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="pricing-card h-100 sharp">
                        <div class="pricing-header p-4 text-center border-bottom">
                            <h3 class="h3 mb-3">Elite</h3>
                            <div class="price mb-0">
                                <span class="currency">$</span>
                                <span class="amount">149</span>
                                <span class="period">/month</span>
                            </div>
                        </div>
                        <div class="pricing-features p-4">
                            <ul class="list-unstyled">
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>24/7 Gym Access</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>All Equipment & Amenities</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>Unlimited Group Classes</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>Comprehensive Fitness Assessment</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>4 Personal Training Sessions/Month</span>
                                </li>
                                <li class="d-flex align-items-center mb-3">
                                    <i class="fas fa-check text-primary me-2"></i>
                                    <span>Monthly Nutrition Consultation</span>
                                </li>
                            </ul>
                        </div>
                        <div class="pricing-footer p-4 text-center">
                            <a href="register.html" class="btn btn-outline-primary w-100 sharp">Choose Plan</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trainers Section -->
    <section id="trainers" class="trainers py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Meet Our Trainers</h2>
                <p class="text-muted"><i>"Expert guidance from <b>certified professionals</b>"</i></p>
            </div>
            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="trainer-card h-100 sharp">
                        <div class="trainer-img position-relative">
                            <img src="https://via.placeholder.com/300x300" alt="Trainer Michael Johnson" class="img-fluid w-100">
                            <div class="trainer-social">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                        <div class="trainer-info p-4">
                            <h3 class="h4 mb-1">Michael Johnson</h3>
                            <p class="trainer-role text-primary fw-medium mb-2">Strength & Conditioning</p>
                            <p class="trainer-desc text-muted">10+ years experience specializing in strength training and athletic performance.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="trainer-card h-100 sharp">
                        <div class="trainer-img position-relative">
                            <img src="https://via.placeholder.com/300x300" alt="Trainer Sarah Williams" class="img-fluid w-100">
                            <div class="trainer-social">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                        <div class="trainer-info p-4">
                            <h3 class="h4 mb-1">Sarah Williams</h3>
                            <p class="trainer-role text-primary fw-medium mb-2">Yoga & Flexibility</p>
                            <p class="trainer-desc text-muted">Certified yoga instructor with expertise in mobility and mind-body connection.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="trainer-card h-100 sharp">
                        <div class="trainer-img position-relative">
                            <img src="https://via.placeholder.com/300x300" alt="Trainer David Chen" class="img-fluid w-100">
                            <div class="trainer-social">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                        <div class="trainer-info p-4">
                            <h3 class="h4 mb-1">David Chen</h3>
                            <p class="trainer-role text-primary fw-medium mb-2">Nutrition & Weight Loss</p>
                            <p class="trainer-desc text-muted">Nutritionist and trainer specializing in sustainable weight management programs.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="trainer-card h-100 sharp">
                        <div class="trainer-img position-relative">
                            <img src="https://via.placeholder.com/300x300" alt="Trainer Jessica Martinez" class="img-fluid w-100">
                            <div class="trainer-social">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                                <a href="#"><i class="fab fa-linkedin-in"></i></a>
                            </div>
                        </div>
                        <div class="trainer-info p-4">
                            <h3 class="h4 mb-1">Jessica Martinez</h3>
                            <p class="trainer-role text-primary fw-medium mb-2">HIIT & Cardio</p>
                            <p class="trainer-desc text-muted">Energy-focused trainer specializing in high-intensity interval training and cardio workouts.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Success Stories</h2>
                <p class="text-muted">What our members say about us</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active">
                                <div class="testimonial-card p-4 sharp">
                                    <div class="d-flex flex-column flex-md-row gap-4">
                                        <div class="testimonial-img">
                                            <img src="https://via.placeholder.com/80x80" alt="Client Testimonial" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="testimonial-content">
                                            <div class="testimonial-rating mb-2">
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                            </div>
                                            <p class="testimonial-text fst-italic mb-3">"EliteFit completely transformed my approach to fitness. The trainers are exceptional and the facilities are top-notch. I've lost 30 pounds and gained confidence I never thought possible."</p>
                                            <div class="testimonial-author">
                                                <h4 class="h5 mb-1">Jennifer K.</h4>
                                                <p class="text-muted small">Member for 1 year</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="testimonial-card p-4 sharp">
                                    <div class="d-flex flex-column flex-md-row gap-4">
                                        <div class="testimonial-img">
                                            <img src="https://via.placeholder.com/80x80" alt="Client Testimonial" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="testimonial-content">
                                            <div class="testimonial-rating mb-2">
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                            </div>
                                            <p class="testimonial-text fst-italic mb-3">"As a busy professional, I needed a gym that could accommodate my schedule. EliteFit's 24/7 access and variety of classes have made it easy to stay consistent with my fitness routine."</p>
                                            <div class="testimonial-author">
                                                <h4 class="h5 mb-1">Robert T.</h4>
                                                <p class="text-muted small">Member for 2 years</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="testimonial-card p-4 sharp">
                                    <div class="d-flex flex-column flex-md-row gap-4">
                                        <div class="testimonial-img">
                                            <img src="https://via.placeholder.com/80x80" alt="Client Testimonial" class="img-fluid rounded-circle">
                                        </div>
                                        <div class="testimonial-content">
                                            <div class="testimonial-rating mb-2">
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star text-warning"></i>
                                                <i class="fas fa-star-half-alt text-warning"></i>
                                            </div>
                                            <p class="testimonial-text fst-italic mb-3">"The nutrition coaching at EliteFit was the missing piece in my fitness journey. I've not only built muscle but also learned how to fuel my body properly for optimal performance."</p>
                                            <div class="testimonial-author">
                                                <h4 class="h5 mb-1">Marcus L.</h4>
                                                <p class="text-muted small">Member for 8 months</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center mt-4">
                            <button class="carousel-control-prev position-relative d-inline-block mx-2" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="prev">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="carousel-control-next position-relative d-inline-block mx-2" type="button" data-bs-target="#testimonialCarousel" data-bs-slide="next">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta py-5">
        <div class="container">
            <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                    <h2 class="h1 text-white mb-4">Ready to Start Your Fitness Journey?</h2>
                    <p class="text-white-50 mb-4">Join EliteFit today and take the first step toward a healthier, stronger you.</p>
                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="register.html" class="btn btn-primary btn-lg sharp">Get Started</a>
                        <a href="#contact" class="btn btn-outline-light btn-lg sharp">Contact Us</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title">Contact Us</h2>
                <p class="text-muted">We're here to answer your questions</p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="contact-card p-4 h-100 sharp">
                                <div class="contact-icon mb-3">
                                    <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                                </div>
                                <h3 class="h4 mb-2">Our Location</h3>
                                <p class="text-muted">86 Spintex Rd<br>00233 - Accra, GH</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-card p-4 h-100 sharp">
                                <div class="contact-icon mb-3">
                                    <i class="fas fa-phone-alt fa-2x text-primary"></i>
                                </div>
                                <h3 class="h4 mb-2">Phone Number</h3>
                                <p class="text-muted">(027) 123-4567</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-card p-4 h-100 sharp">
                                <div class="contact-icon mb-3">
                                    <i class="fas fa-envelope fa-2x text-primary"></i>
                                </div>
                                <h3 class="h4 mb-2">Email Address</h3>
                                <a href="mailto:info@elitefit.com" class="text-primary">info@elitefit.com</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="contact-card p-4 h-100 sharp">
                                <div class="contact-icon mb-3">
                                    <i class="fas fa-clock fa-2x text-primary"></i>
                                </div>
                                <h3 class="h4 mb-2">Working Hours</h3>
                                <p class="text-muted">Monday - Friday: 5AM - 11PM<br>Saturday - Sunday: 7AM - 9PM</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="contact-form-card p-4 sharp">
                        <form id="contactForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" class="form-control sharp" id="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address</label>
                                        <input type="email" class="form-control sharp" id="email" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control sharp" id="phone">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="subject" class="form-label">Subject</label>
                                        <select class="form-select sharp" id="subject">
                                            <option value="membership">Membership Inquiry</option>
                                            <option value="training">Personal Training</option>
                                            <option value="classes">Group Classes</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="message" class="form-label">Message</label>
                                        <textarea class="form-control sharp" id="message" rows="5" required></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary w-100 sharp">Send Message</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d254172.46490891826!2d-0.2817812719123927!3d5.594365641147003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xfdf9084b2b7a773%3A0xbed14ed8650e2dd3!2sAccra%2C%20Ghana!5e0!3m2!1sen!2sus!4v1711095574961!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </section>

    <!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row g-4 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="footer-logo d-flex align-items-center mb-3">
                        <img src="https://via.placeholder.com/40" alt="EliteFit Logo" class="me-2">
                        <span class="text-white h4 mb-0">EliteFit</span>
                    </div>
                    <p class="text-white-50 mb-3">Your premium fitness destination for transformation and excellence.</p>
                    <div class="social-links">
                        <a href="https://facebook.com" target="_blank" class="social-link"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com" target="_blank" class="social-link"><i class="fab fa-twitter"></i></a>
                        <a href="https://instagram.com" target="_blank" class="social-link"><i class="fab fa-instagram"></i></a>
                        <a href="https://linkedin.com" target="_blank" class="social-link"><i class="fab fa-linkedin-in"></i></a>
                        <a href="https://youtube.com" target="_blank" class="social-link"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h3 class="h5 text-white mb-3">Quick Links</h3>
                    <ul class="footer-links list-unstyled">
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#membership">Membership</a></li>
                        <li><a href="#trainers">Trainers</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h3 class="h5 text-white mb-3">Services</h3>
                    <ul class="footer-links list-unstyled">
                        <li><a href="#">Personal Training</a></li>
                        <li><a href="#">Group Classes</a></li>
                        <li><a href="#">Nutrition Coaching</a></li>
                        <li><a href="#">Recovery Services</a></li>
                        <li><a href="#">Fitness Assessment</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6">
                    <h3 class="h5 text-white mb-3">Newsletter</h3>
                    <p class="text-white-50 mb-3">Subscribe to our newsletter for fitness tips, promotions, and updates.</p>
                    <form class="newsletter-form">
                        <div class="input-group">
                            <input type="email" class="form-control sharp" placeholder="Your email address" required>
                            <button class="btn btn-primary sharp" type="submit"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="footer-bottom pt-4 border-top border-secondary">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <p class="text-white-50 mb-0">&copy; 2025 EliteFit. All Rights Reserved.</p>
                    </div>
                    <div class="col-md-6 text-center text-md-end">
                        <div class="footer-bottom-links">
                            <a href="#">Privacy Policy</a>
                            <a href="#">Terms of Service</a>
                            <a href="#">Cookie Policy</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <a href="#" class="back-to-top" id="backToTop">
        <i class="fas fa-chevron-up"></i>
    </a>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="script.js"></script>
</body>
</html>