<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Zone.php');
    exit;
}

$zone_id = $_GET['id'];

// Fetch zone details with region name
$stmt = $pdo->prepare("
    SELECT z.*, r.region_name 
    FROM Zone z 
    JOIN Region r ON z.region_id = r.region_id 
    WHERE z.zone_id = ?
");
$stmt->execute([$zone_id]);
$zone = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$zone) {
    echo "Zone not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Zone</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>View Zone</h2>
        <table class="table table-bordered">
            <tr>
                <th>Zone Name</th>
                <td><?= htmlspecialchars($zone['zone_name']) ?></td>
            </tr>
            <tr>
                <th>Region Name</th>
                <td><?= htmlspecialchars($zone['region_name']) ?></td>
            </tr>
        </table>
        <a href="Zone.php" class="btn btn-primary">Back to Zones</a>
    </div>
</body>
</html>
