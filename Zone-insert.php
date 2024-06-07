<?php
require_once './config/session_check.php';
require './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $region_id = $_POST['region_id'];
    $zone_name = trim($_POST['zone_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($zone_name) || empty($region_id)) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate zone name within the same region
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Zone WHERE region_id = ? AND zone_name = ?");
    $stmt->execute([$region_id, $zone_name]);
    if ($stmt->fetchColumn() > 0) {
        echo "Zone name already exists in this region.";
        exit;
    }

    // Insert new zone
    $stmt = $pdo->prepare("INSERT INTO Zone (region_id, zone_name) VALUES (?, ?)");
    $stmt->execute([$region_id, $zone_name]);

    // Record in audit trail
    $zone_id = $pdo->lastInsertId();
    $details = json_encode(['region_id' => $region_id, 'zone_name' => $zone_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'insert', ?, ?)");
    $audit_stmt->execute([$user_id, $zone_id, $details]);

    echo "Zone added successfully.";
}

// Fetch regions for the dropdown
$stmt = $pdo->query("SELECT * FROM Region");
$regions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Zone</title>
</head>
<body>
    <form method="post" action="Zone-insert.php">
        <label for="region_id">Region:</label>
        <select name="region_id" id="region_id" required>
            <option value="">Select a region</option>
            <?php foreach ($regions as $region): ?>
                <option value="<?= htmlspecialchars($region['region_id']) ?>"><?= htmlspecialchars($region['region_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="zone_name">Zone Name:</label>
        <input type="text" name="zone_name" id="zone_name" required>
        <button type="submit">Add Zone</button>
    </form>
</body>
</html>
