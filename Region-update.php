<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Region.php');
    exit;
}

$region_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $region_name = trim($_POST['region_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($region_name)) {
        echo "Region name is required.";
        exit;
    }

    // Check for duplicate region name
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Region WHERE region_name = ? AND region_id != ?");
    $stmt->execute([$region_name, $region_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "Region name already exists.";
        exit;
    }

    // Update region
    $stmt = $pdo->prepare("UPDATE Region SET region_name = ? WHERE region_id = ?");
    $stmt->execute([$region_name, $region_id]);

    // Record in audit trail
    $details = json_encode(['region_name' => $region_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'update', ?, ?)");
    $audit_stmt->execute([$user_id, $region_id, $details]);

    header('Location: Region.php');
    exit;
}

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
    <title>Update Region</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Update Region</h2>
        <form method="post" action="Region-update.php?id=<?= $region_id ?>">
            <div class="form-group">
                <label for="region_name">Region Name</label>
                <input type="text" name="region_name" id="region_name" class="form-control" value="<?= htmlspecialchars($region['region_name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <a href="Region.php" class="btn btn-secondary mt-3">Back to Regions</a>
    </div>
</body>
</html>
