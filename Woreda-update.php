<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['id'])) {
    header('Location: Woreda.php');
    exit;
}

$woreda_id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_city_id = $_POST['sub_city_id'];
    $woreda_name = trim($_POST['woreda_name']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    if (empty($woreda_name) || empty($sub_city_id)) {
        echo "All fields are required.";
        exit;
    }

    // Check for duplicate woreda name within the same sub-city
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Woreda WHERE sub_city_id = ? AND woreda_name = ? AND woreda_id != ?");
    $stmt->execute([$sub_city_id, $woreda_name, $woreda_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "Woreda name already exists in this sub-city.";
        exit;
    }

    // Update woreda
    $stmt = $pdo->prepare("UPDATE Woreda SET sub_city_id = ?, woreda_name = ? WHERE woreda_id = ?");
    $stmt->execute([$sub_city_id, $woreda_name, $woreda_id]);

    // Record in audit trail
    $details = json_encode(['sub_city_id' => $sub_city_id, 'woreda_name' => $woreda_name]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'update', ?, ?)");
    $audit_stmt->execute([$user_id, $woreda_id, $details]);

    header('Location: Woreda.php');
    exit;
}

// Fetch woreda details
$stmt = $pdo->prepare("SELECT * FROM Woreda WHERE woreda_id = ?");
$stmt->execute([$woreda_id]);
$woreda = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$woreda) {
    echo "Woreda not found.";
    exit;
}

// Fetch sub-cities for the dropdown
$stmt = $pdo->query("SELECT * FROM SubCity");
$subCities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Woreda</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Update Woreda</h2>
        <form method="post" action="Woreda-update.php?id=<?= $woreda_id ?>">
            <div class="form-group">
                <label for="sub_city_id">Sub-city</label>
                <select name="sub_city_id" id="sub_city_id" class="form-control" required>
                    <?php foreach ($subCities as $subCity): ?>
                        <option value="<?= htmlspecialchars($subCity['sub_city_id']) ?>" <?= $subCity['sub_city_id'] == $woreda['sub_city_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($subCity['sub_city_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="woreda_name">Woreda Name</label>
                <input type="text" name="woreda_name" id="woreda_name" class="form-control" value="<?= htmlspecialchars($woreda['woreda_name']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <a href="Woreda.php" class="btn btn-secondary mt-3">Back to Woredas</a>
    </div>
</body>
</html>
