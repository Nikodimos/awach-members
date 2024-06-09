<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['region_id'])) {
    header('Location: region.php');
    exit;
}

$region_id = $_GET['region_id'];

// Fetch the region name for breadcrumb
$region_stmt = $pdo->prepare('SELECT region_name FROM Region WHERE region_id = ?');
$region_stmt->execute([$region_id]);
$region = $region_stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $zone_name = trim($_POST['zone_name']);
    $user_id = $_SESSION['user_id'];

    if (empty($zone_name)) {
        $error = "Zone name is required.";
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Zone WHERE zone_name = ? AND region_id = ?');
        $stmt->execute([$zone_name, $region_id]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Zone name already exists in this region.";
        } else {
            $stmt = $pdo->prepare('INSERT INTO Zone (region_id, zone_name) VALUES (?, ?)');
            if ($stmt->execute([$region_id, $zone_name])) {
                // Record in audit trail
                $details = json_encode(['region_id' => $region_id, 'zone_name' => $zone_name]);
                $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'insert', ?)");
                $audit_stmt->execute([$user_id, $details]);

                header("Location: zone.php?region_id=$region_id");
                exit;
            } else {
                $error = "Failed to add zone.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Zone</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Add Zone</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $region_id; ?>"><?php echo htmlspecialchars($region['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Zone</li>
        </ol>
    </nav>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="zone_add.php?region_id=<?php echo $region_id; ?>">
        <div class="form-group">
            <label for="zone_name">Zone Name</label>
            <input type="text" name="zone_name" id="zone_name" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Zone</button>
    </form>
</div>
</body>
</html>
