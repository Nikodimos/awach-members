<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Woreda.php');
    exit;
}

$woreda_id = $_GET['id'];
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Delete woreda
$stmt = $pdo->prepare("DELETE FROM Woreda WHERE woreda_id = ?");
$stmt->execute([$woreda_id]);

// Record in audit trail
$details = json_encode(['woreda_id' => $woreda_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'delete', ?, ?)");
$audit_stmt->execute([$user_id, $woreda_id, $details]);

header('Location: Woreda.php');
exit;
?>
