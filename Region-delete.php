<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Region.php');
    exit;
}

$region_id = $_GET['id'];
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Delete region
$stmt = $pdo->prepare("DELETE FROM Region WHERE region_id = ?");
$stmt->execute([$region_id]);

// Record in audit trail
$details = json_encode(['region_id' => $region_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'delete', ?, ?)");
$audit_stmt->execute([$user_id, $region_id, $details]);

header('Location: Region.php');
exit;
?>
