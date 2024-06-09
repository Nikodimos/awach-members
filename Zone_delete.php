<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['zone_id'])) {
    header('Location: region.php');
    exit;
}

$zone_id = $_GET['zone_id'];
$region_id = $_GET['region_id'];
$user_id = $_SESSION['user_id'];

// Record in audit trail
$details = json_encode(['zone_id' => $zone_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'delete', ?)");
$audit_stmt->execute([$user_id, $details]);

$stmt = $pdo->prepare('DELETE FROM Zone WHERE zone_id = ?');
$stmt->execute([$zone_id]);

header("Location: zone.php?region_id=$region_id");
exit;
?>
