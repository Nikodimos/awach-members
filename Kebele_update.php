<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['kebele_id'])) {
    header('Location: woreda.php');
    exit;
}

$kebele_id = $_GET['kebele_id'];
$stmt = $pdo->prepare('SELECT * FROM Kebele WHERE kebele_id = ?');
$stmt->execute([$kebele_id]);
$kebele = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$kebele) {
    header('Location: woreda.php');
    exit;
}

// Fetch the woreda, sub-city, city, zone, and region name for breadcrumb
$woreda_stmt = $pdo->prepare('SELECT w.woreda_name, sc.sub_city_name, c.city_name, z.zone_name, r.region_name, sc.sub_city_id, c.city_id, z.zone_id, r.region_id FROM Woreda w JOIN SubCity sc ON w.sub_city_id = sc.sub_city_id JOIN City c ON sc.city_id = c.city_id JOIN Zone z ON c.zone_id = z.zone_id JOIN Region r ON z.region_id = r.region_id WHERE w.woreda_id = ?');
$woreda_stmt->execute([$kebele['woreda_id']]);
$woreda = $woreda_stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kebele_name = trim($_POST['kebele_name']);
    $user_id = $_SESSION['user_id'];

    if (empty($kebele_name)) {
        $error = "Kebele name is required.";
    } else {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM Kebele WHERE kebele_name = ? AND kebele_id != ? AND woreda_id = ?');
        $stmt->execute([$kebele_name, $kebele_id, $kebele['woreda_id']]);

        if ($stmt->fetchColumn() > 0) {
            $error = "Kebele name already exists in this woreda.";
        } else {
            $stmt = $pdo->prepare('UPDATE Kebele SET kebele_name = ? WHERE kebele_id = ?');
            if ($stmt->execute([$kebele_name, $kebele_id])) {
                // Record in audit trail
                $details = json_encode(['kebele_id' => $kebele_id, 'kebele_name' => $kebele_name]);
                $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'update', ?)");
                $audit_stmt->execute([$user_id, $details]);

                header("Location: kebele.php?woreda_id={$kebele['woreda_id']}");
                exit;
            } else {
                $error = "Failed to update kebele.";
            }
        }
    }
}
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
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $woreda['region_id']; ?>"><?php echo htmlspecialchars($woreda['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $woreda['zone_id']; ?>"><?php echo htmlspecialchars($woreda['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="subcity.php?city_id=<?php echo $woreda['city_id']; ?>"><?php echo htmlspecialchars($woreda['city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="woreda.php?sub_city_id=<?php echo $woreda['sub_city_id']; ?>"><?php echo htmlspecialchars($woreda['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($woreda['woreda_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="post" action="kebele_update.php?kebele_id=<?php echo $kebele_id; ?>">
        <div class="form-group">
            <label for="kebele_name">Kebele Name</label>
            <input type="text" name="kebele_name" id="kebele_name" class="form-control" value="<?php echo htmlspecialchars($kebele['kebele_name'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button type="submit" class="btn btn-primary">Update Kebele</button>
    </form>
</div>
</body>
</html>
