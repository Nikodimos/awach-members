<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['city_id'])) {
    header('Location: zone.php');
    exit;
}

$city_id = $_GET['city_id'];
$zone_id = $_GET['zone_id'];
$user_id = $_SESSION['user_id'];

// Record in audit trail
$details = json_encode(['city_id' => $city_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'delete', ?)");
$audit_stmt->execute([$user_id, $details]);

$stmt = $pdo->prepare('DELETE FROM City WHERE city_id = ?');
$stmt->execute([$city_id]);

header("Location: city.php?zone_id=$zone_id");
exit;
?>
