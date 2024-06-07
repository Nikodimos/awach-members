<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Woreda.php');
    exit;
}

$woreda_id = $_GET['id'];

// Fetch woreda details with sub-city, zone, and region names
$stmt = $pdo->prepare("
    SELECT w.*, s.sub_city_name, z.zone_name, r.region_name 
    FROM Woreda w 
    JOIN SubCity s ON w.sub_city_id = s.sub_city_id
    JOIN Zone z ON s.zone_id = z.zone_id
    JOIN Region r ON z.region_id = r.region_id 
    WHERE w.woreda_id = ?
");
$stmt->execute([$woreda_id]);
$woreda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$woreda) {
    echo "Woreda not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Woreda</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>View Woreda</h2>
        <table class="table table-bordered">
            <tr>
                <th>Woreda Name</th>
                <td><?= htmlspecialchars($woreda['woreda_name']) ?></td>
            </tr>
            <tr>
                <th>Sub-city Name</th>
                <td><?= htmlspecialchars($woreda['sub_city_name']) ?></td>
            </tr>
            <tr>
                <th>Zone Name</th>
                <td><?= htmlspecialchars($woreda['zone_name']) ?></td>
            </tr>
            <tr>
                <th>Region Name</th>
                <td><?= htmlspecialchars($woreda['region_name']) ?></td>
            </tr>
        </table>
        <a href="Woreda.php" class="btn btn-primary">Back to Woredas</a>
    </div>
</body>
</html>
