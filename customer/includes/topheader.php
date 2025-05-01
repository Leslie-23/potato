<?php
// Start session safely
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection (Adjust these settings)
$servername = "localhost";
$dbUsername = "new_user"; // replace with your database username
$dbPassword = "new_password"; // replace with your database password
$dbName = "elitefit-23";       // replace with your database name

// Create connection
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize default username
$fetchedUsername = "Customer";

// Check if user is logged in
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Prepare SQL query to fetch the username securely
    $stmt = $conn->prepare("SELECT username FROM members WHERE user_id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $stmt->bind_result($resultUsername);

        if ($stmt->fetch() && !is_null($resultUsername)) {
            $fetchedUsername = $resultUsername;
        }
        
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
$_SESSION['username'] = $resultUsername;

?>

<!-- HTML Part -->
<div id="user-nav" class="navbar navbar-inverse">
  <ul class="nav right">
    <li class="dropdown" id="profile-messages">
      <a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle">
        <i class="icon icon-user"></i>  
        <span class="text">Welcome <?php echo htmlspecialchars($fetchedUsername, ENT_QUOTES, 'UTF-8'); ?></span>
        <b class="caret"></b>
      </a>
      <ul class="dropdown-menu">
        <li><a href="../pages/my-report.php"><i class="icon-user"></i> My Report</a></li>
        <li class="divider"></li>
        <li><a href="to-do.php"><i class="icon-check"></i> My Tasks</a></li>
        <li class="divider"></li>
        <li><a href="../logout.php"><i class="icon-key"></i> Log Out</a></li>
      </ul>
    </li>
    <li class="">
      <a title="" href="../logout.php">
        <i class="icon icon-share-alt"></i> 
        <span class="text">Logout</span>
      </a>
    </li>
  </ul>
</div>
