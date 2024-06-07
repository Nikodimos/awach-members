<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Region.php');
    exit;
}

$region_id = $_GET['id'];

// Fetch region details
$stmt = $pdo->prepare("SELECT * FROM Region WHERE region_id = ?");
$stmt->execute([$region_id]);
$region = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$region) {
    echo "Region not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Region</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>View Region</h2>
        <table class="table table-bordered">
            <tr>
                <th>Region Name</th>
                <td><?= htmlspecialchars($region['region_name']) ?></td>
            </tr>
        </table>
        <a href="Region.php" class="btn btn-primary">Back to Regions</a>
    </div>
</body>
</html>
