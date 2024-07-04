<?php
require_once './config/session_check.php';
require './config/database.php';

if (isset($_POST['zone_id'])) {
    $zone_id = $_POST['zone_id'];
    $stmt = $pdo->prepare('SELECT city_id, city_name FROM City WHERE zone_id = ?');
    $stmt->execute([$zone_id]);
    $cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Select City</option>';
    foreach ($cities as $city) {
        echo '<option value="' . $city['city_id'] . '">' . htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
}
?>
