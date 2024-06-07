<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Kebele.php');
    exit;
}

$kebele_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $woreda_id = $_POST['woreda_id'];
    $kebele_name = trim($_POST['kebele_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($kebele_name) || empty($woreda_id)) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate kebele name within the same woreda
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Kebele WHERE woreda_id = ? AND kebele_name = ? AND kebele_id != ?");
    $stmt->execute([$woreda_id, $kebele_name, $kebele_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "Kebele name already exists in this woreda.";
        exit;
    }

    // Update kebele
    $stmt = $pdo->prepare("UPDATE Kebele SET woreda_id = ?, kebele_name = ? WHERE kebele_id = ?");
    $stmt->execute([$woreda_id, $kebele_name, $kebele_id]);

    // Record in audit trail
    $details = json_encode(['woreda_id' => $woreda_id, 'kebele_name' => $kebele_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'update', ?, ?)");
    $audit_stmt->execute([$user_id, $kebele_id, $details]);

    header('Location: Kebele.php');
    exit;
}

// Fetch kebele details
$stmt = $pdo->prepare("SELECT * FROM Kebele WHERE kebele_id = ?");
$stmt->execute([$kebele_id]);
$kebele = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kebele) {
    echo "Kebele not found.";
    exit;
}

// Fetch woredas for the dropdown
$stmt = $pdo->query("SELECT * FROM Woreda");
$woredas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Kebele</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Update Kebele</h2>
        <form method="post" action="Kebele-update.php?id=<?= $kebele_id ?>">
            <div class="form-group">
                <label for="woreda_id">Woreda</label>
                <select name="woreda_id" id="woreda_id" class="form-control" required>
                    <?php foreach ($woredas as $woreda): ?>
                        <option value="<?= htmlspecialchars($woreda['woreda_id']) ?>" <?= $woreda['woreda_id'] == $kebele['woreda_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($woreda['woreda_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="kebele_name">Kebele Name</label>
                <input type="text" name="kebele_name" id="kebele_name" class="form-control" value="<?= htmlspecialchars($kebele['kebele_name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <a href="Kebele.php" class="btn btn-secondary mt-3">Back to Kebeles</a>
    </div>
</body>
</html>
