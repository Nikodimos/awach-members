<?php
require_once './config/session_check.php';
require './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zone_id = $_POST['zone_id'];
    $sub_city_name = trim($_POST['sub_city_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($sub_city_name) || empty($zone_id)) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate sub-city name within the same zone
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SubCity WHERE zone_id = ? AND sub_city_name = ?");
    $stmt->execute([$zone_id, $sub_city_name]);
    if ($stmt->fetchColumn() > 0) {
        echo "Sub-city name already exists in this zone.";
        exit;
    }

    // Insert new sub-city
    $stmt = $pdo->prepare("INSERT INTO SubCity (zone_id, sub_city_name) VALUES (?, ?)");
    $stmt->execute([$zone_id, $sub_city_name]);

    // Record in audit trail
    $sub_city_id = $pdo->lastInsertId();
    $details = json_encode(['zone_id' => $zone_id, 'sub_city_name' => $sub_city_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'insert', ?, ?)");
    $audit_stmt->execute([$user_id, $sub_city_id, $details]);

    echo "Sub-city added successfully.";
}

// Fetch zones for the dropdown
$stmt = $pdo->query("SELECT * FROM Zone");
$zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Sub-city</title>
</head>
<body>
    <form method="post" action="Subcity-insert.php">
        <label for="zone_id">Zone:</label>
        <select name="zone_id" id="zone_id" required>
            <option value="">Select a zone</option>
            <?php foreach ($zones as $zone): ?>
                <option value="<?= htmlspecialchars($zone['zone_id']) ?>"><?= htmlspecialchars($zone['zone_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="sub_city_name">Sub-city Name:</label>
        <input type="text" name="sub_city_name" id="sub_city_name" required>
        <button type="submit">Add Sub-city</button>
    </form>
</body>
</html>
