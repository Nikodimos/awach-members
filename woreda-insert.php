<?php
require_once './config/session_check.php';
require './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_city_id = $_POST['sub_city_id'];
    $woreda_name = trim($_POST['woreda_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($woreda_name) || empty($sub_city_id)) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate woreda name within the same sub-city
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Woreda WHERE sub_city_id = ? AND woreda_name = ?");
    $stmt->execute([$sub_city_id, $woreda_name]);
    if ($stmt->fetchColumn() > 0) {
        echo "Woreda name already exists in this sub-city.";
        exit;
    }

    // Insert new woreda
    $stmt = $pdo->prepare("INSERT INTO Woreda (sub_city_id, woreda_name) VALUES (?, ?)");
    $stmt->execute([$sub_city_id, $woreda_name]);

    // Record in audit trail
    $woreda_id = $pdo->lastInsertId();
    $details = json_encode(['sub_city_id' => $sub_city_id, 'woreda_name' => $woreda_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'insert', ?, ?)");
    $audit_stmt->execute([$user_id, $woreda_id, $details]);

    echo "Woreda added successfully.";
}

// Fetch sub-cities for the dropdown
$stmt = $pdo->query("SELECT * FROM SubCity");
$subCities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Woreda</title>
</head>
<body>
    <form method="post" action="Woreda-insert.php">
        <label for="sub_city_id">Sub-city:</label>
        <select name="sub_city_id" id="sub_city_id" required>
            <option value="">Select a sub-city</option>
            <?php foreach ($subCities as $subCity): ?>
                <option value="<?= htmlspecialchars($subCity['sub_city_id']) ?>"><?= htmlspecialchars($subCity['sub_city_name']) ?></option>
            <?php endforeach; ?>
        </select>
        <label for="woreda_name">Woreda Name:</label>
        <input type="text" name="woreda_name" id="woreda_name" required>
        <button type="submit">Add Woreda</button>
    </form>
</body>
</html>
