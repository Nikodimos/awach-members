<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Title.php');
    exit;
}

$title_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title_name = trim($_POST['title_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($title_name)) {
        echo "Title name is required.";
        exit;
    }

    // Check for duplicate title name
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Title WHERE title_name = ? AND title_id != ?");
    $stmt->execute([$title_name, $title_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "Title name already exists.";
        exit;
    }

    // Update title
    $stmt = $pdo->prepare("UPDATE Title SET title_name = ? WHERE title_id = ?");
    $stmt->execute([$title_name, $title_id]);

    // Record in audit trail
    $details = json_encode(['title_name' => $title_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'update', ?, ?)");
    $audit_stmt->execute([$user_id, $title_id, $details]);

    header('Location: Title.php');
    exit;
}

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
    <title>Update Title</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Update Title</h2>
        <form method="post" action="Title-update.php?id=<?= $title_id ?>">
            <div class="form-group">
                <label for="title_name">Title Name</label>
                <input type="text" name="title_name" id="title_name" class="form-control" value="<?= htmlspecialchars($title['title_name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <a href="Title.php" class="btn btn-secondary mt-3">Back to Titles</a>
    </div>
</body>
</html>
