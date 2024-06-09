<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['city_id'])) {
    header('Location: zone.php');
    exit;
}

$city_id = $_GET['city_id'];
$stmt = $pdo->prepare('SELECT * FROM City WHERE city_id = ?');
$stmt->execute([$city_id]);
$city = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$city) {
    header('Location: zone.php');
    exit;
}

// Fetch the zone and region name for breadcrumb
$zone_stmt = $pdo->prepare('SELECT z.zone_name, r.region_name, z.region_id FROM Zone z JOIN Region r ON z.region_id = r.region_id WHERE z.zone_id = ?');
$zone_stmt->execute([$city['zone_id']]);
$zone = $zone_stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $city_name = trim($_POST['city_name']);
    $user_id = $_SESSION['user_id'];

    if (empty($city_name)) {
        $error = "City name is required.";
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM City WHERE city_name = ? AND city_id != ? AND zone_id = ?');
        $stmt->execute([$city_name, $city_id, $city['zone_id']]);

        if ($stmt->fetchColumn() > 0) {
            $error = "City name already exists in this zone.";
        } else {
            $stmt = $pdo->prepare('UPDATE City SET city_name = ? WHERE city_id = ?');
            if ($stmt->execute([$city_name, $city_id])) {
                // Record in audit trail
                $details = json_encode(['city_id' => $city_id, 'city_name' => $city_name]);
                $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'update', ?)");
                $audit_stmt->execute([$user_id, $details]);

                header("Location: city.php?zone_id={$city['zone_id']}");
                exit;
            } else {
                $error = "Failed to update city.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update City</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Update City</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $zone['region_id']; ?>"><?php echo htmlspecialchars($zone['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $city['zone_id']; ?>"><?php echo htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="city_update.php?city_id=<?php echo $city_id; ?>">
        <div class="form-group">
            <label for="city_name">City Name</label>
            <input type="text" name="city_name" id="city_name" class="form-control" value="<?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update City</button>
    </form>
</div>
</body>
</html>
