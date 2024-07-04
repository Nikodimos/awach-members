<?php
require_once './config/session_check.php';
require './config/database.php';

if (isset($_POST['region_id'])) {
    $region_id = $_POST['region_id'];
    $stmt = $pdo->prepare('SELECT zone_id, zone_name FROM Zone WHERE region_id = ?');
    $stmt->execute([$region_id]);
    $zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Select Zone</option>';
    foreach ($zones as $zone) {
        echo '<option value="' . $zone['zone_id'] . '">' . htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
}
?>
