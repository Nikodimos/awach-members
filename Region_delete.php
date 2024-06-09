<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['region_id'])) {
    header('Location: region.php');
    exit;
}

$region_id = $_GET['region_id'];
$user_id = $_SESSION['user_id'];

// Record in audit trail
$details = json_encode(['region_id' => $region_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'delete', ?)");
$audit_stmt->execute([$user_id, $details]);

$stmt = $pdo->prepare('DELETE FROM Region WHERE region_id = ?');
$stmt->execute([$region_id]);

header('Location: region.php');
exit;
?>
