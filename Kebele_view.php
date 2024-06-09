<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['kebele_id'])) {
    header('Location: woreda.php');
    exit;
}

$kebele_id = $_GET['kebele_id'];
$stmt = $pdo->prepare('SELECT * FROM Kebele WHERE kebele_id = ?');
$stmt->execute([$kebele_id]);
$kebele = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kebele) {
    header('Location: woreda.php');
    exit;
}

// Fetch the woreda, sub-city, city, zone, and region name for breadcrumb
$woreda_stmt = $pdo->prepare('SELECT w.woreda_name, sc.sub_city_name, c.city_name, z.zone_name, r.region_name, sc.sub_city_id, c.city_id, z.zone_id, r.region_id FROM Woreda w JOIN SubCity sc ON w.sub_city_id = sc.sub_city_id JOIN City c ON sc.city_id = c.city_id JOIN Zone z ON c.zone_id = z.zone_id JOIN Region r ON z.region_id = r.region_id WHERE w.woreda_id = ?');
$woreda_stmt->execute([$kebele['woreda_id']]);
$woreda = $woreda_stmt->fetch(PDO::FETCH_ASSOC);
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
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $woreda['region_id']; ?>"><?php echo htmlspecialchars($woreda['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $woreda['zone_id']; ?>"><?php echo htmlspecialchars($woreda['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="subcity.php?city_id=<?php echo $woreda['city_id']; ?>"><?php echo htmlspecialchars($woreda['city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="woreda.php?sub_city_id=<?php echo $woreda['sub_city_id']; ?>"><?php echo htmlspecialchars($woreda['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($woreda['woreda_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <table class="table table-bordered">
        <tr>
            <th>Kebele ID</th>
            <td><?php echo $kebele['kebele_id']; ?></td>
        </tr>
        <tr>
            <th>Kebele Name</th>
            <td><?php echo htmlspecialchars($kebele['kebele_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    </table>
    <a href="kebele.php?woreda_id=<?php echo $kebele['woreda_id']; ?>" class="btn btn-secondary">Back to Kebeles</a>
</div>
</body>
</html>
