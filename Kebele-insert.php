<?php
require_once './config/session_check.php';
require './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $woreda_id = $_POST['woreda_id'];
    $kebele_name = trim($_POST['kebele_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($kebele_name) || empty($woreda_id)) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate kebele name within the same woreda
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Kebele WHERE woreda_id = ? AND kebele_name = ?");
    $stmt->execute([$woreda_id, $kebele_name]);
    if ($stmt->fetchColumn() > 0) {
        echo "Kebele name already exists in this woreda.";
        exit;
    }

    // Insert new kebele
    $stmt = $pdo->prepare("INSERT INTO Kebele (woreda_id, kebele_name) VALUES (?, ?)");
    $stmt->execute([$woreda_id, $kebele_name]);

    // Record in audit trail
    $kebele_id = $pdo->lastInsertId();
    $details = json_encode(['woreda_id' => $woreda_id, 'kebele_name' => $kebele_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'insert', ?, ?)");
    $audit_stmt->execute([$user_id, $kebele_id, $details]);

    echo "Kebele added successfully.";
}

// Fetch woredas for the dropdown
$stmt = $pdo->query("SELECT * FROM Woreda");
$woredas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Kebele</title>
</head>
<body>
    <form method="post" action="Kebele-insert.php">
        <label for="woreda_id">Woreda:</label>
        <select name="woreda_id" id="woreda_id" required>
            <option value="">Select a woreda</option>
            <?php foreach ($woredas as $woreda): ?>
                <option value="<?= htmlspecialchars($woreda['woreda_id']) ?>"><?= htmlspecialchars($woreda['woreda_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="kebele_name">Kebele Name:</label>
        <input type="text" name="kebele_name" id="kebele_name" required>
        <button type="submit">Add Kebele</button>
    </form>
</body>
</html>
