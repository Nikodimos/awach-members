<?php
require_once './config/session_check.php';
require './config/database.php';

// Fetch all kebeles with woreda, sub-city, zone, and region names
$stmt = $pdo->query("
    SELECT k.*, w.woreda_name, s.sub_city_name, z.zone_name, r.region_name 
    FROM Kebele k 
    JOIN Woreda w ON k.woreda_id = w.woreda_id
    JOIN SubCity s ON w.sub_city_id = s.sub_city_id
    JOIN Zone z ON s.zone_id = z.zone_id
    JOIN Region r ON z.region_id = r.region_id
");
$kebeles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kebeles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Kebeles</h2>
        <a href="Kebele-insert.php" class="btn btn-primary mb-3">Add Kebele</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Kebele Name</th>
                    <th>Woreda Name</th>
                    <th>Sub-city Name</th>
                    <th>Zone Name</th>
                    <th>Region Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($kebeles as $kebele): ?>
                    <tr>
                        <td><?= htmlspecialchars($kebele['kebele_name']) ?></td>
                        <td><?= htmlspecialchars($kebele['woreda_name']) ?></td>
                        <td><?= htmlspecialchars($kebele['sub_city_name']) ?></td>
                        <td><?= htmlspecialchars($kebele['zone_name']) ?></td>
                        <td><?= htmlspecialchars($kebele['region_name']) ?></td>
                        <td>
                            <a href="Kebele-view.php?id=<?= $kebele['kebele_id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="Kebele-update.php?id=<?= $kebele['kebele_id'] ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="Kebele-delete.php?id=<?= $kebele['kebele_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
