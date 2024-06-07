<?php
require_once './config/session_check.php';
require './config/database.php';

// Fetch all woredas with sub-city, zone, and region names
$stmt = $pdo->query("
    SELECT w.*, s.sub_city_name, z.zone_name, r.region_name 
    FROM Woreda w 
    JOIN SubCity s ON w.sub_city_id = s.sub_city_id
    JOIN Zone z ON s.zone_id = z.zone_id
    JOIN Region r ON z.region_id = r.region_id
");
$woredas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Woredas</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Woredas</h2>
        <a href="Woreda-insert.php" class="btn btn-primary mb-3">Add Woreda</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Woreda Name</th>
                    <th>Sub-city Name</th>
                    <th>Zone Name</th>
                    <th>Region Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($woredas as $woreda): ?>
                    <tr>
                        <td><?= htmlspecialchars($woreda['woreda_name']) ?></td>
                        <td><?= htmlspecialchars($woreda['sub_city_name']) ?></td>
                        <td><?= htmlspecialchars($woreda['zone_name']) ?></td>
                        <td><?= htmlspecialchars($woreda['region_name']) ?></td>
                        <td>
                            <a href="Woreda-view.php?id=<?= $woreda['woreda_id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="Woreda-update.php?id=<?= $woreda['woreda_id'] ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="Woreda-delete.php?id=<?= $woreda['woreda_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
