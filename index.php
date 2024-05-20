<?php
require './config/database.php';
require './config/check_session.php';

try {
    // Check if the database exists by trying to fetch data from the 'users' table
    $result = $pdo->query("SELECT 1 FROM users LIMIT 1");

    // If the query runs successfully, the tables exist
    header("Location: login.php"); // Redirect to login or the main application page
    exit;
} catch (PDOException $e) {
    // Check if the error is due to a missing table
    if ($e->getCode() == '42S02') { // MySQL error code for "table does not exist"
        header("Location: initialize.php"); // Redirect to initialization script
        exit;
    } elseif ($e->getCode() == '1049') { // MySQL error code for "unknown database"
        echo "The database 'sacco_db' does not exist. Please create the database.";
    } else {
        echo "An error occurred: " . $e->getMessage();
    }
}
?>
