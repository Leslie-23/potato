<?php
// Start session safely with security settings
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => true,    // Only send cookies over HTTPS
        'cookie_httponly' => true,  // Prevent JavaScript access to session cookie
        'use_strict_mode' => true   // Prevent session fixation
    ]);
}

// Regenerate session ID periodically to prevent fixation
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 180000) { // 300 minutes
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
if (!isset($_SESSION['user_id'])) {
    // header("HTTP/1.1 403 Forbidden");
    header("Location: ../login.php");
    echo '<script>window.location = "../login.php";</script>';
    echo '<script>alert("You are not logged in - session may have expired");</script>';
    exit();
}
// Check whether the session variable user_id is present and valid
if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id']) || $_SESSION['user_id'] <= 0) {
    header("HTTP/1.1 403 Forbidden");
    header("Location: ../login.php");
    exit();
}


$session_id = (int)$_SESSION['user_id']; // Ensure integer type
?>