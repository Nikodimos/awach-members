<?php
session_start();
require './config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $branch_id = $_POST['branch_id'];
    $role_id = $_POST['role_id'];
    $password = password_hash('123456', PASSWORD_DEFAULT); // Default password
    $status = 'pending';
    $created_by = $_SESSION['user_id'];
    $created_at = date('Y-m-d H:i:s');

    // Server-side validation
    if (empty($full_name) || empty($username) || empty($branch_id) || empty($role_id)) {
        echo "<script>alert('All fields are required.'); window.history.back();</script>";
        exit;
    }

    // Check for username duplication
    $usernameCheckQuery = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $usernameCheckQuery->execute([$username]);
    if ($usernameCheckQuery->fetchColumn() > 0) {
        echo "<script>alert('Username already exists.'); window.history.back();</script>";
        exit;
    }

    // Insert user into users table
    $insertUserQuery = $pdo->prepare("INSERT INTO users (username, password, full_name, branch_id, status, created_at, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insertUserQuery->execute([$username, $password, $full_name, $branch_id, $status, $created_at, $created_by]);
    $newUserId = $pdo->lastInsertId();

    // Insert user role into user_roles table
    $insertRoleQuery = $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)");
    $insertRoleQuery->execute([$newUserId, $role_id]);

    // Record in audit trail
    $details = json_encode([
        'username' => $username,
        'full_name' => $full_name,
        'branch_id' => $branch_id,
        'role_id' => $role_id,
        'status' => $status
    ]);
    $auditQuery = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, timestamp, details) VALUES (?, ?, ?, ?, ?)");
    $auditQuery->execute([$created_by, 'User created', $newUserId, $created_at, $details]);

    header("Location: user.php");
    exit;
} else {
    header("Location: user.php");
    exit;
}
?>