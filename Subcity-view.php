<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Subcity.php');
    exit;
}

$sub_city_id = $_GET['id'];

// Fetch sub-city details with zone and region names
$stmt = $pdo->prepare("
    SELECT s.*, z.zone_name, r.region_name 
    FROM SubCity s 
    JOIN Zone z ON s.zone_id = z.zone_id
    JOIN Region r ON z.region_id = r.region_id 
    WHERE s.sub_city_id = ?
");
$stmt->execute([$sub_city_id]);
$subCity = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subCity) {
    echo "Sub-city not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Sub-city</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>View Sub-city</h2>
        <table class="table table-bordered">
            <tr>
                <th>Sub-city Name</th>
                <td><?= htmlspecialchars($subCity['sub_city_name']) ?></td>
            </tr>
            <tr>
                <th>Zone Name</th>
                <td><?= htmlspecialchars($subCity['zone_name']) ?></td>
            </tr>
            <tr>
                <th>Region Name</th>
                <td><?= htmlspecialchars($subCity['region_name']) ?></td>
            </tr>
        </table>
        <a href="Subcity.php" class="btn btn-primary">Back to Sub-cities</a>
    </div>
</body>
</html>
