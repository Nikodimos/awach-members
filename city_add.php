<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['zone_id'])) {
    header('Location: zone.php');
    exit;
}

$zone_id = $_GET['zone_id'];

// Fetch the zone and region name for breadcrumb
$zone_stmt = $pdo->prepare('SELECT z.zone_name, r.region_name, z.region_id FROM Zone z JOIN Region r ON z.region_id = r.region_id WHERE z.zone_id = ?');
$zone_stmt->execute([$zone_id]);
$zone = $zone_stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city_name = trim($_POST['city_name']);
    $user_id = $_SESSION['user_id'];

    if (empty($city_name)) {
        $error = "City name is required.";
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM City WHERE city_name = ? AND zone_id = ?');
        $stmt->execute([$city_name, $zone_id]);

        if ($stmt->fetchColumn() > 0) {
            $error = "City name already exists in this zone.";
        } else {
            $stmt = $pdo->prepare('INSERT INTO City (zone_id, city_name) VALUES (?, ?)');
            if ($stmt->execute([$zone_id, $city_name])) {
                // Record in audit trail
                $details = json_encode(['zone_id' => $zone_id, 'city_name' => $city_name]);
                $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'insert', ?)");
                $audit_stmt->execute([$user_id, $details]);

                header("Location: city.php?zone_id=$zone_id");
                exit;
            } else {
                $error = "Failed to add city.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add City</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Add City</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $zone['region_id']; ?>"><?php echo htmlspecialchars($zone['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $zone_id; ?>"><?php echo htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Add City</li>
        </ol>
    </nav>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="city_add.php?zone_id=<?php echo $zone_id; ?>">
        <div class="form-group">
            <label for="city_name">City Name</label>
            <input type="text" name="city_name" id="city_name" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add City</button>
    </form>
</div>
</body>
</html>
