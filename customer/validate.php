<?php
session_start();

// Database configuration - UPDATE THESE WITH YOUR DETAILS
$servername = "localhost";
$username = "new_user";
$password = "new_password";
$dbname = "elitefit-23";

// Create database connection
$con = new mysqli($servername, $username, $password, $dbname);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Get email from session if available
$email = $_SESSION['email'] ?? null;

// Handle OTP generation and sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $email = $_POST['email'];
    $_SESSION['email'] = $email; // Store email in session
    
    // Generate 6-digit OTP
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['otp_time'] = time();

    // Store OTP in database (using your actual column names)
    $stmt = $con->prepare("UPDATE members SET otp = ?, otp_expiry = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE email = ?");
    $stmt->bind_param("is", $otp, $email);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        $_SESSION['error'] = "Email not found in our system.";
        header("Location: validate.php");
        exit();
    }
    
    $stmt->close();
    
    // Send OTP via EmailJS - UPDATE THESE WITH YOUR EMAILJS DETAILS
    ?>
    <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
    <script>
        (function() {
            // Initialize EmailJS with your user ID
            emailjs.init("6W98kc4gPVLdCp2RV"); // REPLACE WITH YOUR EMAILJS USER ID
            
            var templateParams = {
                to_email: '<?php echo $email; ?>',
                otp: '<?php echo $otp; ?>'
            };
            
            // Send email using your service ID and template ID
            emailjs.send('service_x3zoj59', 'template_7he6yyj', templateParams) // REPLACE WITH YOUR SERVICE AND TEMPLATE IDs
                .then(function(response) {
                    console.log('OTP sent successfully', response);
                }, function(error) {
                    console.log('Failed to send OTP', error);
                    alert('Failed to send OTP. Please try again.');
                });
        })();
    </script>
    <?php
}

// Handle OTP verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $user_otp = $_POST['otp'];
    $email = $_SESSION['email'];
    
    // Verify OTP from database
    $stmt = $con->prepare("SELECT user_id FROM members WHERE email = ? AND otp = ? AND otp_expiry > NOW()");
    $stmt->bind_param("si", $email, $user_otp);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // OTP is valid - activate user (using 'Active' to match your database)
        $update = $con->prepare("UPDATE members SET status = 'Active', otp = NULL, otp_expiry = NULL, verified_at = NOW() WHERE email = ?");
        $update->bind_param("s", $email);
        $update->execute();
        $update->close();
        
        $_SESSION['verified'] = true;
        unset($_SESSION['otp']); // Clear OTP from session
        header("Location: ./login.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid or expired OTP. Please try again.";
        header("Location: validate.php");
        exit();
    }
}

// Resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    unset($_SESSION['otp']);
    unset($_SESSION['otp_time']);
    header("Location: validate.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification | EliteFit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;  /* Main brand blue */
            --secondary-color: #2c3e50; /* Dark blue */
            --success-color: #2ecc71;  /* Green */
            --danger-color: #e74c3c;   /* Red */
            --light-color: #ecf0f1;    /* Light gray */
        }
        
        body {
            background-color: #2c3e50;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .verification-card {
            max-width: 500px;
            margin: 5rem auto;
            border-radius: 0px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background-color: var(--secondary-color);
            color: white;
            padding: 1.5rem;
            text-align: center;
        }
        
        .card-body {
            padding: 2rem;
            background-color: white;
        }
        
        .otp-input {
            letter-spacing: 15px;
            font-size: 28px;
            text-align: center;
            padding: 10px;
            font-weight: bold;
            color: var(--secondary-color);
            border: 2px solid #ddd;
            border-radius: 0px;
        }
        
        .otp-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px;
            font-weight: 600;
        }
        
        .btn-success {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }
        
        .alert {
            border-radius: 0px;
        }
        
        .resend-btn {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .resend-btn:hover {
            background-color: var(--primary-color);
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="verification-card">
            <div class="card-header">
                <h3 class="mb-0"><i class="fas fa-envelope me-2"></i>Email Verification</h3>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?php echo htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (!isset($_SESSION['otp'])): ?>
                    <!-- Email Input Form -->
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="email" class="form-label fw-bold">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control py-2" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                       placeholder="Enter your email address" required>
                            </div>
                            <div class="invalid-feedback">
                                Please provide a valid email address.
                            </div>
                        </div>
                        <button type="submit" name="send_otp" class="btn btn-primary w-100 py-2">
                            <i class="fas fa-paper-plane me-2"></i> Send OTP
                        </button>
                    </form>
                <?php else: ?>
                    <!-- OTP Verification Form -->
                    <form method="POST" class="needs-validation" novalidate>
                        <div class="text-center mb-4">
                            <i class="fas fa-envelope-open-text fa-3x mb-3" style="color: var(--primary-color);"></i>
                            <p class="lead">We've sent a 6-digit verification code to:</p>
                            <p class="fw-bold text-primary"><?php echo htmlspecialchars($email); ?></p>
                            <small class="text-muted">Check your inbox and enter the code below</small>
                        </div>
                        
                        <div class="mb-4">
                            <label for="otp" class="form-label fw-bold">Verification Code</label>
                            <input type="text" class="form-control otp-input" id="otp" name="otp" 
                                   maxlength="6" pattern="\d{6}" required
                                   placeholder="••••••">
                            <div class="invalid-feedback">
                                Please enter the 6-digit code you received.
                            </div>
                        </div>
                        
                        <div class="d-grid gap-3">
                            <button type="submit" name="verify_otp" class="btn btn-success py-2">
                                <i class="fas fa-check-circle me-2"></i> Verify Code
                            </button>
                            <button type="submit" name="resend_otp" class="btn btn-outline-primary py-2">
                                <i class="fas fa-sync-alt me-2"></i> Resend Code
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            
            // Fetch all forms we want to apply custom validation styles to
            var forms = document.querySelectorAll('.needs-validation')
            
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
        
        // Auto-advance OTP input
        document.getElementById('otp')?.addEventListener('input', function(e) {
            if (this.value.length === 6) {
                this.blur(); // Remove focus when complete
            }
        });
    </script>
</body>
</html>