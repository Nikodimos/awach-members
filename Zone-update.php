<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Zone.php');
    exit;
}

$zone_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $region_id = $_POST['region_id'];
    $zone_name = trim($_POST['zone_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($zone_name) || empty($region_id)) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate zone name within the same region
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Zone WHERE region_id = ? AND zone_name = ? AND zone_id != ?");
    $stmt->execute([$region_id, $zone_name, $zone_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "Zone name already exists in this region.";
        exit;
    }

    // Update zone
    $stmt = $pdo->prepare("UPDATE Zone SET region_id = ?, zone_name = ? WHERE zone_id = ?");
    $stmt->execute([$region_id, $zone_name, $zone_id]);

    // Record in audit trail
    $details = json_encode(['region_id' => $region_id, 'zone_name' => $zone_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'update', ?, ?)");
    $audit_stmt->execute([$user_id, $zone_id, $details]);

    header('Location: Zone.php');
    exit;
}

// Fetch zone details
$stmt = $pdo->prepare("SELECT * FROM Zone WHERE zone_id = ?");
$stmt->execute([$zone_id]);
$zone = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$zone) {
    echo "Zone not found.";
    exit;
}

// Fetch regions for the dropdown
$stmt = $pdo->query("SELECT * FROM Region");
$regions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Zone</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Update Zone</h2>
        <form method="post" action="Zone-update.php?id=<?= $zone_id ?>">
            <div class="form-group">
                <label for="region_id">Region</label>
                <select name="region_id" id="region_id" class="form-control" required>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?= htmlspecialchars($region['region_id']) ?>" <?= $region['region_id'] == $zone['region_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($region['region_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="zone_name">Zone Name</label>
                <input type="text" name="zone_name" id="zone_name" class="form-control" value="<?= htmlspecialchars($zone['zone_name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <a href="Zone.php" class="btn btn-secondary mt-3">Back to Zones</a>
    </div>
</body>
</html>
