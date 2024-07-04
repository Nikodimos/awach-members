<?php
require_once './config/session_check.php';
require './config/database.php';

if (isset($_POST['sub_city_id'])) {
    $sub_city_id = $_POST['sub_city_id'];
    $stmt = $pdo->prepare('SELECT woreda_id, woreda_name FROM Woreda WHERE sub_city_id = ?');
    $stmt->execute([$sub_city_id]);
    $woredas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo '<option value="">Select Woreda</option>';
    foreach ($woredas as $woreda) {
        echo '<option value="' . $woreda['woreda_id'] . '">' . htmlspecialchars($woreda['woreda_name'], ENT_QUOTES, 'UTF-8') . '</option>';
    }
}
?>
