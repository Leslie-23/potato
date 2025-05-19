<?php
// Start session safely with security settings
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => true,    // Only send cookies over HTTPS
        'cookie_httponly' => true,  // Prevent JavaScript access to session cookie
        'use_strict_mode' => true   // Prevent session fixation
    ]);
}

// Database connection (Adjust these settings)
$servername = "localhost";
$dbUsername = "new_user";     // replace with your database username
$dbPassword = "new_password"; // replace with your database password
$dbName = "elitefit-23";      // replace with your database name

// Initialize default username
$fetchedUsername = "Customer";
$resultUsername = null;

try {
    // Create connection
    $con = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
    
    // Check connection
    if ($con->connect_error) {
        throw new Exception("Connection failed: " . $con->connect_error);
    }

    // Check if user is logged in
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        // Prepare SQL query to fetch the username securely
        $stmt = $con->prepare("SELECT username FROM members WHERE user_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($resultUsername);

            if ($stmt->fetch() && !empty($resultUsername)) {
                $fetchedUsername = $resultUsername;
            }
            
            $stmt->close();
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    // You might want to handle this error more gracefully in production
} finally {
    // Close the database connection if it exists
    if (isset($con)) {
        $con->close();
    }
}

// Only store username in session if we actually got a valid one
if (!empty($resultUsername)) {
    $_SESSION['username'] = $resultUsername;
}
?>

<!-- HTML Part -->
<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav right">
    <li class="dropdown" id="profile-messages">
      <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle">
        <i class="fas fa-user"></i>  
        <span class="text">Welcome <b><?php echo htmlspecialchars($fetchedUsername, ENT_QUOTES, 'UTF-8'); ?></b></span>
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <li><a href="../pages/my-report.php"><i class="fas fa-file-alt"></i> My Report</a></li>
        <li class="divider"></li>
        <li><a href="to-do.php"><i class="fas fa-tasks"></i> My Tasks</a></li>
        <li class="divider"></li>
        <li><a href="../pages/profile.php"><i class="fas fa-user-cog"></i> My Profile</a></li>
        <li class="divider"></li>
        <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
      </ul>
    </li>
    <li class="">
      <a title="" href="../logout.php">
        <i class="fas fa-sign-out-alt"></i> 
        <span class="text">Logout</span>
      </a>
    </li>
  </ul>
</div>