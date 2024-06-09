<?php
require_once './config/session_check.php';
require './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $region_name = trim($_POST['region_name']);
    $user_id = $_SESSION['user_id'];

    if (empty($region_name)) {
        $error = "Region name is required.";
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Region WHERE region_name = ?');
        $stmt->execute([$region_name]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Region name already exists.";
        } else {
            $stmt = $pdo->prepare('INSERT INTO Region (region_name) VALUES (?)');
            if ($stmt->execute([$region_name])) {
                // Record in audit trail
                $details = json_encode(['region_name' => $region_name]);
                $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'insert', ?)");
                $audit_stmt->execute([$user_id, $details]);

                header('Location: region.php');
                exit;
            } else {
                $error = "Failed to add region.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Region</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Add Region</h2>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="region_add.php">
        <div class="form-group">
            <label for="region_name">Region Name</label>
            <input type="text" name="region_name" id="region_name" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Region</button>
    </form>
</div>
</body>
</html>
