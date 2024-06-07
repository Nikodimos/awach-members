<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Kebele.php');
    exit;
}

$kebele_id = $_GET['id'];

// Fetch kebele details with woreda, sub-city, zone, and region names
$stmt = $pdo->prepare("
    SELECT k.*, w.woreda_name, s.sub_city_name, z.zone_name, r.region_name 
    FROM Kebele k 
    JOIN Woreda w ON k.woreda_id = w.woreda_id
    JOIN SubCity s ON w.sub_city_id = s.sub_city_id
    JOIN Zone z ON s.zone_id = z.zone_id
    JOIN Region r ON z.region_id = r.region_id 
    WHERE k.kebele_id = ?
");
$stmt->execute([$kebele_id]);
$kebele = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kebele) {
    echo "Kebele not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Kebele</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>View Kebele</h2>
        <table class="table table-bordered">
            <tr>
                <th>Kebele Name</th>
                <td><?= htmlspecialchars($kebele['kebele_name']) ?></td>
            </tr>
            <tr>
                <th>Woreda Name</th>
                <td><?= htmlspecialchars($kebele['woreda_name']) ?></td>
            </tr>
            <tr>
                <th>Sub-city Name</th>
                <td><?= htmlspecialchars($kebele['sub_city_name']) ?></td>
            </tr>
            <tr>
                <th>Zone Name</th>
                <td><?= htmlspecialchars($kebele['zone_name']) ?></td>
            </tr>
            <tr>
                <th>Region Name</th>
                <td><?= htmlspecialchars($kebele['region_name']) ?></td>
            </tr>
        </table>
        <a href="Kebele.php" class="btn btn-primary">Back to Kebeles</a>
    </div>
</body>
</html>
