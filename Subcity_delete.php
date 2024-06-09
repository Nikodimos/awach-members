<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['sub_city_id'])) {
    header('Location: city.php');
    exit;
}

$sub_city_id = $_GET['sub_city_id'];
$city_id = $_GET['city_id'];
$user_id = $_SESSION['user_id'];

// Record in audit trail
$details = json_encode(['sub_city_id' => $sub_city_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'delete', ?)");
$audit_stmt->execute([$user_id, $details]);

$stmt = $pdo->prepare('DELETE FROM SubCity WHERE sub_city_id = ?');
$stmt->execute([$sub_city_id]);

header("Location: subcity.php?city_id=$city_id");
exit;
?>
