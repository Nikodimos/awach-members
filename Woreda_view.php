<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['woreda_id'])) {
    header('Location: subcity.php');
    exit;
}

$woreda_id = $_GET['woreda_id'];
$stmt = $pdo->prepare('SELECT * FROM Woreda WHERE woreda_id = ?');
$stmt->execute([$woreda_id]);
$woreda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$woreda) {
    header('Location: subcity.php');
    exit;
}

// Fetch the sub-city, city, zone, and region name for breadcrumb
$sub_city_stmt = $pdo->prepare('SELECT sc.sub_city_name, c.city_name, z.zone_name, r.region_name, c.city_id, z.zone_id, r.region_id FROM SubCity sc JOIN City c ON sc.city_id = c.city_id JOIN Zone z ON c.zone_id = z.zone_id JOIN Region r ON z.region_id = r.region_id WHERE sc.sub_city_id = ?');
$sub_city_stmt->execute([$woreda['sub_city_id']]);
$sub_city = $sub_city_stmt->fetch(PDO::FETCH_ASSOC);

$kebeles_stmt = $pdo->prepare('SELECT * FROM Kebele WHERE woreda_id = ?');
$kebeles_stmt->execute([$woreda_id]);
$kebeles = $kebeles_stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $sub_city['region_id']; ?>"><?php echo htmlspecialchars($sub_city['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $sub_city['zone_id']; ?>"><?php echo htmlspecialchars($sub_city['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="subcity.php?city_id=<?php echo $sub_city['city_id']; ?>"><?php echo htmlspecialchars($sub_city['city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($sub_city['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <table class="table table-bordered">
        <tr>
            <th>Woreda ID</th>
            <td><?php echo $woreda['woreda_id']; ?></td>
        </tr>
        <tr>
            <th>Woreda Name</th>
            <td><?php echo htmlspecialchars($woreda['woreda_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    </table>
    <h3>Kebeles in <?php echo htmlspecialchars($woreda['woreda_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
    <?php if (empty($kebeles)): ?>
        <div class="alert alert-info">There are no kebeles recorded in this woreda.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Kebele Name</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($kebeles as $kebele): ?>
            <tr>
                <td><?php echo htmlspecialchars($kebele['kebele_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <a href="woreda.php?sub_city_id=<?php echo $woreda['sub_city_id']; ?>" class="btn btn-secondary">Back to Woredas</a>
</div>
</body>
</html>
