<?php
require_once './config/session_check.php';
require './config/database.php';

if (isset($_POST['zone_id'])) {
    $zone_id = $_POST['zone_id'];
    $stmt = $pdo->prepare("SELECT * FROM SubCity WHERE zone_id = ?");
    $stmt->execute([$zone_id]);
    echo '<option value="">Select a Sub-city</option>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . htmlspecialchars($row['sub_city_id']) . '">' . htmlspecialchars($row['sub_city_name']) . '</option>';
    }
}
?>
