<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['zone_id'])) {
    header('Location: region.php');
    exit;
}

$zone_id = $_GET['zone_id'];
$stmt = $pdo->prepare('SELECT * FROM Zone WHERE zone_id = ?');
$stmt->execute([$zone_id]);
$zone = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$zone) {
    header('Location: region.php');
    exit;
}

// Fetch the region name for breadcrumb
$region_stmt = $pdo->prepare('SELECT region_name FROM Region WHERE region_id = ?');
$region_stmt->execute([$zone['region_id']]);
$region = $region_stmt->fetch(PDO::FETCH_ASSOC);

$cities_stmt = $pdo->prepare('SELECT * FROM City WHERE zone_id = ?');
$cities_stmt->execute([$zone_id]);
$cities = $cities_stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $zone['region_id']; ?>"><?php echo htmlspecialchars($region['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <table class="table table-bordered">
        <tr>
            <th>Zone ID</th>
            <td><?php echo $zone['zone_id']; ?></td>
        </tr>
        <tr>
            <th>Zone Name</th>
            <td><?php echo htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    </table>
    <h3>Cities in <?php echo htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
    <?php if (empty($cities)): ?>
        <div class="alert alert-info">There are no cities recorded in this zone.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>City Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cities as $city): ?>
            <tr>
                <td><?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <a href="zone.php?region_id=<?php echo $zone['region_id']; ?>" class="btn btn-secondary">Back to Zones</a>
</div>
</body>
</html>
