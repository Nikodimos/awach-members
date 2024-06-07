<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Zone.php');
    exit;
}

$zone_id = $_GET['id'];
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Delete zone
$stmt = $pdo->prepare("DELETE FROM Zone WHERE zone_id = ?");
$stmt->execute([$zone_id]);

// Record in audit trail
$details = json_encode(['zone_id' => $zone_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'delete', ?, ?)");
$audit_stmt->execute([$user_id, $zone_id, $details]);

header('Location: Zone.php');
exit;
?>
