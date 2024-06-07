<?php
require_once './config/session_check.php';
require './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_name = trim($_POST['title_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($title_name)) {
        echo "Title name is required.";
        exit;
    }

    // Check for duplicate title name
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Title WHERE title_name = ?");
    $stmt->execute([$title_name]);
    if ($stmt->fetchColumn() > 0) {
        echo "Title name already exists.";
        exit;
    }

    // Insert new title
    $stmt = $pdo->prepare("INSERT INTO Title (title_name) VALUES (?)");
    $stmt->execute([$title_name]);

    // Record in audit trail
    $title_id = $pdo->lastInsertId();
    $details = json_encode(['title_name' => $title_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'insert', ?, ?)");
    $audit_stmt->execute([$user_id, $title_id, $details]);

    echo "Title added successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Title</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Insert Title</h2>
        <form method="post" action="Title-insert.php">
            <div class="form-group">
                <label for="title_name">Title Name</label>
                <input type="text" name="title_name" id="title_name" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Title</button>
        </form>
    </div>
</body>
</html>
