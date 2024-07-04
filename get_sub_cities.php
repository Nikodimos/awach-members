<?php
require_once './config/session_check.php';
require './config/database.php';

if (isset($_POST['city_id'])) {
    $city_id = $_POST['city_id'];
    $stmt = $pdo->prepare('SELECT sub_city_id, sub_city_name FROM SubCity WHERE city_id = ?');
    $stmt->execute([$city_id]);
    $sub_cities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Select Sub-City</option>';
    foreach ($sub_cities as $sub_city) {
        echo '<option value="' . $sub_city['sub_city_id'] . '">' . htmlspecialchars($sub_city['sub_city_name'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
}
?>
