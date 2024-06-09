<?php
require_once './config/session_check.php';
require './config/database.php';

if (isset($_POST['sub_city_id'])) {
    $sub_city_id = $_POST['sub_city_id'];
    $stmt = $pdo->prepare("SELECT * FROM Woreda WHERE sub_city_id = ?");
    $stmt->execute([$sub_city_id]);
    echo '<option value="">Select a Woreda</option>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . htmlspecialchars($row['woreda_id']) . '">' . htmlspecialchars($row['woreda_name']) . '</option>';
    }
}
?>
