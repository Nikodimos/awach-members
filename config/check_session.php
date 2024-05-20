<?php
session_start();

// Check if timeout is reached
if (isset($_SESSION['timeout']) && $_SESSION['timeout'] < time()) {
    // Clear all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to login page
    header("Location: login.php");
    exit;
} else {
    // Update timeout
    $_SESSION['timeout'] = time() + 300; // 300 seconds = 5 minutes
}
?>
