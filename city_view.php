<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['city_id'])) {
    header('Location: zone.php');
    exit;
}

$city_id = $_GET['city_id'];
$stmt = $pdo->prepare('SELECT * FROM City WHERE city_id = ?');
$stmt->execute([$city_id]);
$city = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$city) {
    header('Location: zone.php');
    exit;
}

// Fetch the zone and region name for breadcrumb
$zone_stmt = $pdo->prepare('SELECT z.zone_name, r.region_name, z.region_id FROM Zone z JOIN Region r ON z.region_id = r.region_id WHERE z.zone_id = ?');
$zone_stmt->execute([$city['zone_id']]);
$zone = $zone_stmt->fetch(PDO::FETCH_ASSOC);

$subcities_stmt = $pdo->prepare('SELECT * FROM SubCity WHERE city_id = ?');
$subcities_stmt->execute([$city_id]);
$subcities = $subcities_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View City</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>View City</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $zone['region_id']; ?>"><?php echo htmlspecialchars($zone['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $city['zone_id']; ?>"><?php echo htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <table class="table table-bordered">
        <tr>
            <th>City ID</th>
            <td><?php echo $city['city_id']; ?></td>
        </tr>
        <tr>
            <th>City Name</th>
            <td><?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    </table>
    <h3>Sub-Cities in <?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
    <?php if (empty($subcities)): ?>
        <div class="alert alert-info">There are no sub-cities recorded in this city.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Sub-City Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($subcities as $subcity): ?>
            <tr>
                <td><?php echo htmlspecialchars($subcity['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <a href="city.php?zone_id=<?php echo $city['zone_id']; ?>" class="btn btn-secondary">Back to Cities</a>
</div>
</body>
</html>
