<?php
require_once './config/session_check.php';
require './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $region_name = trim($_POST['region_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($region_name)) {
        echo "Region name is required.";
        exit;
    }

    // Check for duplicate region name
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Region WHERE region_name = ?");
    $stmt->execute([$region_name]);
    if ($stmt->fetchColumn() > 0) {
        echo "Region name already exists.";
        exit;
    }

    // Insert new region
    $stmt = $pdo->prepare("INSERT INTO Region (region_name) VALUES (?)");
    $stmt->execute([$region_name]);

    // Record in audit trail
    $region_id = $pdo->lastInsertId();
    $details = json_encode(['region_name' => $region_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'insert', ?, ?)");
    $audit_stmt->execute([$user_id, $region_id, $details]);

    echo "Region added successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Region</title>
</head>
<body>
    <form method="post" action="Region-insert.php">
        <label for="region_name">Region Name:</label>
        <input type="text" name="region_name" id="region_name" required>
        <button type="submit">Add Region</button>
    </form>
</body>
</html>
