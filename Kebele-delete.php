<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Kebele.php');
    exit;
}

$kebele_id = $_GET['id'];
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

// Delete kebele
$stmt = $pdo->prepare("DELETE FROM Kebele WHERE kebele_id = ?");
$stmt->execute([$kebele_id]);

// Record in audit trail
$details = json_encode(['kebele_id' => $kebele_id]);
$audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'delete', ?, ?)");
$audit_stmt->execute([$user_id, $kebele_id, $details]);

header('Location: Kebele.php');
exit;
?>
