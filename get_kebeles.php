<?php
require_once './config/session_check.php';
require './config/database.php';

if (isset($_POST['woreda_id'])) {
    $woreda_id = $_POST['woreda_id'];
    $stmt = $pdo->prepare('SELECT kebele_id, kebele_name FROM Kebele WHERE woreda_id = ?');
    $stmt->execute([$woreda_id]);
    $kebeles = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Select Kebele</option>';
    foreach ($kebeles as $kebele) {
        echo '<option value="' . $kebele['kebele_id'] . '">' . htmlspecialchars($kebele['kebele_name'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
}
?>
