<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Subcity.php');
    exit;
}

$sub_city_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zone_id = $_POST['zone_id'];
    $sub_city_name = trim($_POST['sub_city_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($sub_city_name) || empty($zone_id)) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate sub-city name within the same zone
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM SubCity WHERE zone_id = ? AND sub_city_name = ? AND sub_city_id != ?");
    $stmt->execute([$zone_id, $sub_city_name, $sub_city_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "Sub-city name already exists in this zone.";
        exit;
    }

    // Update sub-city
    $stmt = $pdo->prepare("UPDATE SubCity SET zone_id = ?, sub_city_name = ? WHERE sub_city_id = ?");
    $stmt->execute([$zone_id, $sub_city_name, $sub_city_id]);

    // Record in audit trail
    $details = json_encode(['zone_id' => $zone_id, 'sub_city_name' => $sub_city_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'update', ?, ?)");
    $audit_stmt->execute([$user_id, $sub_city_id, $details]);

    header('Location: Subcity.php');
    exit;
}

// Fetch sub-city details
$stmt = $pdo->prepare("SELECT * FROM SubCity WHERE sub_city_id = ?");
$stmt->execute([$sub_city_id]);
$subCity = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subCity) {
    echo "Sub-city not found.";
    exit;
}

// Fetch zones for the dropdown
$stmt = $pdo->query("SELECT * FROM Zone");
$zones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Sub-city</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Update Sub-city</h2>
        <form method="post" action="Subcity-update.php?id=<?= $sub_city_id ?>">
            <div class="form-group">
                <label for="zone_id">Zone</label>
                <select name="zone_id" id="zone_id" class="form-control" required>
                    <?php foreach ($zones as $zone): ?>
                        <option value="<?= htmlspecialchars($zone['zone_id']) ?>" <?= $zone['zone_id'] == $subCity['zone_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($zone['zone_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="sub_city_name">Sub-city Name</label>
                <input type="text" name="sub_city_name" id="sub_city_name" class="form-control" value="<?= htmlspecialchars($subCity['sub_city_name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <a href="Subcity.php" class="btn btn-secondary mt-3">Back to Sub-cities</a>
    </div>
</body>
</html>
