<?php
require_once './config/session_check.php';
require './config/database.php';

if (isset($_POST['woreda_id'])) {
    $woreda_id = $_POST['woreda_id'];
    $stmt = $pdo->prepare("SELECT * FROM Kebele WHERE woreda_id = ?");
    $stmt->execute([$woreda_id]);
    echo '<option value="">Select a Kebele</option>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . htmlspecialchars($row['kebele_id']) . '">' . htmlspecialchars($row['kebele_name']) . '</option>';
    }
}
?>
