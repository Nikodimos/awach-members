<?php
require_once './config/session_check.php';
require './config/database.php';

if (isset($_POST['region_id'])) {
    $region_id = $_POST['region_id'];
    $stmt = $pdo->prepare("SELECT * FROM Zone WHERE region_id = ?");
    $stmt->execute([$region_id]);
    echo '<option value="">Select a Zone</option>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . htmlspecialchars($row['zone_id']) . '">' . htmlspecialchars($row['zone_name']) . '</option>';
    }
}
?>
