<?php
session_start();

// DB CONNECTION
$con = new mysqli("localhost", "new_user", "new_password", "elitefit-23");
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

function sanitize($data) {
    return htmlspecialchars(trim($data));
}

// Step 1: Validate email and send OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $email = sanitize($_POST['email']);
    // $username = sanitize($_POST['username']); // COMMENTED OUT
    // $age = $_POST['date_of_birth']; // COMMENTED OUT

    // Validation based on only email
    $stmt = $con->prepare("SELECT * FROM members WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['email'] = $email;
        // $_SESSION['username'] = $username; // COMMENTED OUT
        // $_SESSION['date_of_birth'] = $age; // COMMENTED OUT

        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_time'] = time();

        $stmt = $con->prepare("UPDATE members SET otp = ?, otp_expiry = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE email = ?");
        $stmt->bind_param("is", $otp, $email);
        $stmt->execute();
        $stmt->close();

        // Send OTP via EmailJS
        echo "<script>
            emailjs.init('6W98kc4gPVLdCp2RV');
            emailjs.send('service_x3zoj59', 'template_7he6yyj', {
                to_email: '$email',
                otp: '$otp'
            }).then(() => {
                alert('OTP sent to your email.');
            }).catch(err => {
                alert('Failed to send OTP. Try again.');
            });
        </script>";
    } else {
        $_SESSION['error'] = 'Email not found.';
        header("Location: forgot-password.php");
        exit();
    }
}

// Step 2: Verify OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $user_otp = $_POST['otp'];
    $email = $_SESSION['email'];

    $stmt = $con->prepare("SELECT * FROM members WHERE email = ? AND otp = ? AND otp_expiry > NOW()");
    $stmt->bind_param("si", $email, $user_otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['otp_verified'] = true;
        $_SESSION['verified_user'] = $email;
        unset($_SESSION['otp']);
        header("Location: forgot-password.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid or expired OTP.";
        header("Location: forgot-password.php");
        exit();
    }
}

// Step 3: Reset Password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token mismatch.");
    }

    if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
        die("Unauthorized attempt.");
    }

    $new_password = $_POST['password'];
    if (strlen($new_password) < 6) {
        $_SESSION['error'] = "Password must be at least 6 characters.";
        header("Location: forgot-password.php");
        exit();
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $email = $_SESSION['verified_user'];

    $stmt = $con->prepare("UPDATE members SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
    $stmt->bind_param("ss", $hashed_password, $email);
    $stmt->execute();

    unset($_SESSION['otp_verified']);
    unset($_SESSION['verified_user']);
    $_SESSION['success'] = "Password successfully updated. You can now login.";
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Forgot Password | EliteFit</title>
    <script src="https://cdn.jsdelivr.net/npm/emailjs-com@3/dist/email.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-dark text-light">
<div class="container mt-5">
    <div class="card mx-auto" style="max-width: 500px;">
        <div class="card-body">
            <h4 class="card-title mb-4 text-center">Forgot Password</h4>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <?php if (!isset($_SESSION['email'])): ?>
                <!-- Step 1: Enter Email -->
                <form method="POST">
                    <div class="mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <!-- Username & DOB fields commented out
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Date of Birth</label>
                        <input type="date" name="date_of_birth" class="form-control">
                    </div>
                    -->
                    <button name="send_otp" class="btn btn-primary w-100">Send OTP</button>
                </form>

            <?php elseif (!isset($_SESSION['otp_verified'])): ?>
                <!-- Step 2: OTP -->
                <form method="POST">
                    <div class="mb-3">
                        <label>Enter OTP sent to <?php echo $_SESSION['email']; ?></label>
                        <input type="text" name="otp" class="form-control" maxlength="6" required>
                    </div>
                    <button name="verify_otp" class="btn btn-success w-100">Verify OTP</button>
                </form>

            <?php else: ?>
                <!-- Step 3: Reset Password -->
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                    <div class="mb-3">
                        <label>New Password</label>
                        <input type="password" name="password" class="form-control" minlength="6" required>
                    </div>
                    <button name="reset_password" class="btn btn-warning w-100">Reset Password</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
