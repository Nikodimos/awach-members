<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['kebele_id'])) {
    header('Location: woreda.php');
    exit;
}

$kebele_id = $_GET['kebele_id'];
$woreda_id = $_GET['woreda_id'];
$user_id = $_SESSION['user_id'];

// Record in audit trail
$details = json_encode(['kebele_id' => $kebele_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'delete', ?)");
$audit_stmt->execute([$user_id, $details]);

$stmt = $pdo->prepare('DELETE FROM Kebele WHERE kebele_id = ?');
$stmt->execute([$kebele_id]);

header("Location: kebele.php?woreda_id=$woreda_id");
exit;
?>
