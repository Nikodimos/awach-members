<?php
require_once './config/database.php';
// Function to check if tables exist
function tablesExist($pdo) {
    try {
        $result = $pdo->query("SHOW TABLES LIKE 'users'");
        return $result && $result->rowCount() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

try {
    // Check if database connection is successful
    $pdo->query('SELECT 1');

    // Check if tables exist
    if (!tablesExist($pdo)) {
        header("Location: initialize.php");
        exit;
    }

    // Include session check
    require './config/session_check.php';

    // If user is logged in, redirect to dashboard
    header("Location: dashboard.php");
    exit;
} catch (PDOException $e) {
    // Handle database connection error
    echo "<p>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
