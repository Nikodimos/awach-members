<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['woreda_id'])) {
    header('Location: subcity.php');
    exit;
}

$woreda_id = $_GET['woreda_id'];
$sub_city_id = $_GET['sub_city_id'];
$user_id = $_SESSION['user_id'];

// Record in audit trail
$details = json_encode(['woreda_id' => $woreda_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'delete', ?)");
$audit_stmt->execute([$user_id, $details]);

$stmt = $pdo->prepare('DELETE FROM Woreda WHERE woreda_id = ?');
$stmt->execute([$woreda_id]);

header("Location: woreda.php?sub_city_id=$sub_city_id");
exit;
?>
