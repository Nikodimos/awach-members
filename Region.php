<?php
require_once './config/session_check.php';
require './config/database.php';

// Fetch all regions
$stmt = $pdo->query("SELECT * FROM Region");
$regions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Regions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Regions</h2>
        <a href="Region-insert.php" class="btn btn-primary mb-3">Add Region</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Region Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($regions as $region): ?>
                    <tr>
                        <td><?= htmlspecialchars($region['region_name']) ?></td>
                        <td>
                            <a href="Region-view.php?id=<?= $region['region_id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="Region-update.php?id=<?= $region['region_id'] ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="Region-delete.php?id=<?= $region['region_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
