<?php
require_once './config/session_check.php';
require './config/database.php';

// Fetch all zones with region names
$stmt = $pdo->query("
    SELECT z.*, r.region_name 
    FROM Zone z 
    JOIN Region r ON z.region_id = r.region_id
");
$zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zones</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Zones</h2>
        <a href="Zone-insert.php" class="btn btn-primary mb-3">Add Zone</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Zone Name</th>
                    <th>Region Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($zones as $zone): ?>
                    <tr>
                        <td><?= htmlspecialchars($zone['zone_name']) ?></td>
                        <td><?= htmlspecialchars($zone['region_name']) ?></td>
                        <td>
                            <a href="Zone-view.php?id=<?= $zone['zone_id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="Zone-update.php?id=<?= $zone['zone_id'] ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="Zone-delete.php?id=<?= $zone['zone_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
