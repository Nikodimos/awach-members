<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['sub_city_id'])) {
    header('Location: city.php');
    exit;
}

$sub_city_id = $_GET['sub_city_id'];
$stmt = $pdo->prepare('SELECT * FROM SubCity WHERE sub_city_id = ?');
$stmt->execute([$sub_city_id]);
$sub_city = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sub_city) {
    header('Location: city.php');
    exit;
}

// Fetch the city, zone, and region name for breadcrumb
$city_stmt = $pdo->prepare('SELECT c.city_name, z.zone_name, r.region_name, z.zone_id, r.region_id FROM City c JOIN Zone z ON c.zone_id = z.zone_id JOIN Region r ON z.region_id = r.region_id WHERE c.city_id = ?');
$city_stmt->execute([$sub_city['city_id']]);
$city = $city_stmt->fetch(PDO::FETCH_ASSOC);

$woredas_stmt = $pdo->prepare('SELECT * FROM Woreda WHERE sub_city_id = ?');
$woredas_stmt->execute([$sub_city_id]);
$woredas = $woredas_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Sub-City</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>View Sub-City</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $city['region_id']; ?>"><?php echo htmlspecialchars($city['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $city['zone_id']; ?>"><?php echo htmlspecialchars($city['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="subcity.php?city_id=<?php echo $sub_city['city_id']; ?>"><?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($sub_city['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <table class="table table-bordered">
        <tr>
            <th>Sub-City ID</th>
            <td><?php echo $sub_city['sub_city_id']; ?></td>
        </tr>
        <tr>
            <th>Sub-City Name</th>
            <td><?php echo htmlspecialchars($sub_city['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    </table>
    <h3>Woredas in <?php echo htmlspecialchars($sub_city['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
    <?php if (empty($woredas)): ?>
        <div class="alert alert-info">There are no woredas recorded in this sub-city.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Woreda Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($woredas as $woreda): ?>
            <tr>
                <td><?php echo htmlspecialchars($woreda['woreda_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <a href="subcity.php?city_id=<?php echo $sub_city['city_id']; ?>" class="btn btn-secondary">Back to Sub-Cities</a>
</div>
</body>
</html>
