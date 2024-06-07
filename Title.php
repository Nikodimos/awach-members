<?php
require_once './config/session_check.php';
require './config/database.php';

// Fetch all titles
$stmt = $pdo->query("SELECT * FROM Title");
$titles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Titles</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Titles</h2>
        <a href="Title-insert.php" class="btn btn-primary mb-3">Add Title</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($titles as $title): ?>
                    <tr>
                        <td><?= htmlspecialchars($title['title_name']) ?></td>
                        <td>
                            <a href="Title-view.php?id=<?= $title['title_id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="Title-update.php?id=<?= $title['title_id'] ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="Title-delete.php?id=<?= $title['title_id'] ?>" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
