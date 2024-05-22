<?php
session_start();

// Function to handle session timeout
function checkSessionTimeout() {
    $timeout_duration = 300; // 5 minutes
    if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();
        session_destroy();
        header("Location: login.php?message=Session timed out, please log in again.");
        exit;
    }
    $_SESSION['LAST_ACTIVITY'] = time();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Update last activity time
checkSessionTimeout();
?>
