<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['region_id'])) {
    header('Location: region.php');
    exit;
}

$region_id = $_GET['region_id'];
$stmt = $pdo->prepare('SELECT * FROM Region WHERE region_id = ?');
$stmt->execute([$region_id]);
$region = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$region) {
    header('Location: region.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $region_name = trim($_POST['region_name']);
    $user_id = $_SESSION['user_id'];

    if (empty($region_name)) {
        $error = "Region name is required.";
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Region WHERE region_name = ? AND region_id != ?');
        $stmt->execute([$region_name, $region_id]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Region name already exists.";
        } else {
            $stmt = $pdo->prepare('UPDATE Region SET region_name = ? WHERE region_id = ?');
            if ($stmt->execute([$region_name, $region_id])) {
                // Record in audit trail
                $details = json_encode(['region_id' => $region_id, 'region_name' => $region_name]);
                $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'update', ?)");
                $audit_stmt->execute([$user_id, $details]);

                header('Location: region.php');
                exit;
            } else {
                $error = "Failed to update region.";
            }
        }
    }
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
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="region_update.php?region_id=<?php echo $region_id; ?>">
        <div class="form-group">
            <label for="region_name">Region Name</label>
            <input type="text" name="region_name" id="region_name" class="form-control" value="<?php echo htmlspecialchars($region['region_name'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Region</button>
    </form>
</div>
</body>
</html>
