<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['city_id'])) {
    header('Location: city.php');
    exit;
}

$city_id = $_GET['city_id'];

// Fetch the city, zone, and region name for breadcrumb
$city_stmt = $pdo->prepare('SELECT c.city_name, z.zone_name, r.region_name, z.zone_id, r.region_id FROM City c JOIN Zone z ON c.zone_id = z.zone_id JOIN Region r ON z.region_id = r.region_id WHERE c.city_id = ?');
$city_stmt->execute([$city_id]);
$city = $city_stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sub_city_name = trim($_POST['sub_city_name']);
    $user_id = $_SESSION['user_id'];

    if (empty($sub_city_name)) {
        $error = "Sub-City name is required.";
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM SubCity WHERE sub_city_name = ? AND city_id = ?');
        $stmt->execute([$sub_city_name, $city_id]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Sub-City name already exists in this city.";
        } else {
            $stmt = $pdo->prepare('INSERT INTO SubCity (city_id, sub_city_name) VALUES (?, ?)');
            if ($stmt->execute([$city_id, $sub_city_name])) {
                // Record in audit trail
                $details = json_encode(['city_id' => $city_id, 'sub_city_name' => $sub_city_name]);
                $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'insert', ?)");
                $audit_stmt->execute([$user_id, $details]);

                header("Location: subcity.php?city_id=$city_id");
                exit;
            } else {
                $error = "Failed to add sub-city.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Sub-City</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Add Sub-City</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $city['region_id']; ?>"><?php echo htmlspecialchars($city['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $city['zone_id']; ?>"><?php echo htmlspecialchars($city['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="subcity.php?city_id=<?php echo $city_id; ?>"><?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Add Sub-City</li>
        </ol>
    </nav>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="subcity_add.php?city_id=<?php echo $city_id; ?>">
        <div class="form-group">
            <label for="sub_city_name">Sub-City Name</label>
            <input type="text" name="sub_city_name" id="sub_city_name" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Add Sub-City</button>
    </form>
</div>
</body>
</html>
