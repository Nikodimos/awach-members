<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['sub_city_id'])) {
    header('Location: subcity.php');
    exit;
}

$sub_city_id = $_GET['sub_city_id'];

// Fetch the sub-city, city, zone, and region name for breadcrumb
$sub_city_stmt = $pdo->prepare('SELECT sc.sub_city_name, c.city_name, z.zone_name, r.region_name, c.city_id, z.zone_id, r.region_id FROM SubCity sc JOIN City c ON sc.city_id = c.city_id JOIN Zone z ON c.zone_id = z.zone_id JOIN Region r ON z.region_id = r.region_id WHERE sc.sub_city_id = ?');
$sub_city_stmt->execute([$sub_city_id]);
$sub_city = $sub_city_stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $woreda_name = trim($_POST['woreda_name']);
    $user_id = $_SESSION['user_id'];

    if (empty($woreda_name)) {
        $error = "Woreda name is required.";
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Woreda WHERE woreda_name = ? AND sub_city_id = ?');
        $stmt->execute([$woreda_name, $sub_city_id]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Woreda name already exists in this sub-city.";
        } else {
            $stmt = $pdo->prepare('INSERT INTO Woreda (sub_city_id, woreda_name) VALUES (?, ?)');
            if ($stmt->execute([$sub_city_id, $woreda_name])) {
                // Record in audit trail
                $details = json_encode(['sub_city_id' => $sub_city_id, 'woreda_name' => $woreda_name]);
                $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'insert', ?)");
                $audit_stmt->execute([$user_id, $details]);

                header("Location: woreda.php?sub_city_id=$sub_city_id");
                exit;
            } else {
                $error = "Failed to add woreda.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Woreda</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Add Woreda</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $sub_city['region_id']; ?>"><?php echo htmlspecialchars($sub_city['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $sub_city['zone_id']; ?>"><?php echo htmlspecialchars($sub_city['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="subcity.php?city_id=<?php echo $sub_city['city_id']; ?>"><?php echo htmlspecialchars($sub_city['city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="woreda.php?sub_city_id=<?php echo $sub_city_id; ?>"><?php echo htmlspecialchars($sub_city['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Woreda</li>
        </ol>
    </nav>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="woreda_add.php?sub_city_id=<?php echo $sub_city_id; ?>">
        <div class="form-group">
            <label for="woreda_name">Woreda Name</label>
            <input type="text" name="woreda_name" id="woreda_name" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Woreda</button>
    </form>
</div>
</body>
</html>
