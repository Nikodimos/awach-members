<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Subcity.php');
    exit;
}

$sub_city_id = $_GET['id'];
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Delete sub-city
$stmt = $pdo->prepare("DELETE FROM SubCity WHERE sub_city_id = ?");
$stmt->execute([$sub_city_id]);

// Record in audit trail
$details = json_encode(['sub_city_id' => $sub_city_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'delete', ?, ?)");
$audit_stmt->execute([$user_id, $sub_city_id, $details]);

header('Location: Subcity.php');
exit;
?>
