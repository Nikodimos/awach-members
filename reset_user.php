<?php
require_once './config/session_check.php';
require './config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "User ID is required.";
    exit;
}

$user_id = $_GET['id'];
$new_password = password_hash('123456', PASSWORD_DEFAULT);
$updated_by = $_SESSION['user_id'];
$updated_at = date('Y-m-d H:i:s');

// Reset user password and status
$resetUserQuery = $pdo->prepare("UPDATE users SET password = ?, status = 'pending', updated_at = ?, updated_by = ? WHERE user_id = ?");
$resetUserQuery->execute([$new_password, $updated_at, $updated_by, $user_id]);

// Record in audit trail
$details = json_encode(['new_password' => '123456', 'status' => 'pending']);
$auditQuery = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, timestamp, details) VALUES (?, ?, ?, ?, ?)");
$auditQuery->execute([$updated_by, 'Password reset', $user_id, $updated_at, $details]);

header("Location: view_user.php?id=$user_id");
exit;
?>
