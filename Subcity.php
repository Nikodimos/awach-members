<?php
require_once './config/session_check.php';
require './config/database.php';

// Fetch all sub-cities with zone and region names
$stmt = $pdo->query("
    SELECT s.*, z.zone_name, r.region_name 
    FROM SubCity s 
    JOIN Zone z ON s.zone_id = z.zone_id
    JOIN Region r ON z.region_id = r.region_id
");
$subCities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sub-cities</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Sub-cities</h2>
        <a href="Subcity-insert.php" class="btn btn-primary mb-3">Add Sub-city</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Sub-city Name</th>
                    <th>Zone Name</th>
                    <th>Region Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subCities as $subCity): ?>
                    <tr>
                        <td><?= htmlspecialchars($subCity['sub_city_name']) ?></td>
                        <td><?= htmlspecialchars($subCity['zone_name']) ?></td>
                        <td><?= htmlspecialchars($subCity['region_name']) ?></td>
                        <td>
                            <a href="Subcity-view.php?id=<?= $subCity['sub_city_id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="Subcity-update.php?id=<?= $subCity['sub_city_id'] ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="Subcity-delete.php?id=<?= $subCity['sub_city_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
