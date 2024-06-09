<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['region_id'])) {
    header('Location: region.php');
    exit;
}

$region_id = $_GET['region_id'];
$stmt = $pdo->prepare('SELECT * FROM Region WHERE region_id = ?');
$stmt->execute([$region_id]);
$region = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$region) {
    header('Location: region.php');
    exit;
}

$zones_stmt = $pdo->prepare('SELECT * FROM Zone WHERE region_id = ?');
$zones_stmt->execute([$region_id]);
$zones = $zones_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Region</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>View Region</h2>
    <table class="table table-bordered">
        <tr>
            <th>Region ID</th>
            <td><?php echo $region['region_id']; ?></td>
        </tr>
        <tr>
            <th>Region Name</th>
            <td><?php echo htmlspecialchars($region['region_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    </table>
    <h3>Zones in <?php echo htmlspecialchars($region['region_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
    <?php if (empty($zones)): ?>
        <div class="alert alert-info">There are no zones recorded in this region.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Zone Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($zones as $zone): ?>
            <tr>
                <td><?php echo htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <a href="region.php" class="btn btn-secondary">Back to Regions</a>
</div>
</body>
</html>
