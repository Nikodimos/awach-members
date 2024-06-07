<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Title.php');
    exit;
}

$title_id = $_GET['id'];
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Delete title
$stmt = $pdo->prepare("DELETE FROM Title WHERE title_id = ?");
$stmt->execute([$title_id]);

// Record in audit trail
$details = json_encode(['title_id' => $title_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'delete', ?, ?)");
$audit_stmt->execute([$user_id, $title_id, $details]);

header('Location: Title.php');
exit;
?>
