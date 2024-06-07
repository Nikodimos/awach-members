<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Title.php');
    exit;
}

$title_id = $_GET['id'];

// Fetch title details
$stmt = $pdo->prepare("SELECT * FROM Title WHERE title_id = ?");
$stmt->execute([$title_id]);
$title = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$title) {
    echo "Title not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Title</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>View Title</h2>
        <table class="table table-bordered">
            <tr>
                <th>Title Name</th>
                <td><?= htmlspecialchars($title['title_name']) ?></td>
            </tr>
        </table>
        <a href="Title.php" class="btn btn-primary">Back to Titles</a>
    </div>
</body>
</html>
