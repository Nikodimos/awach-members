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

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = $search ? "AND city_name LIKE ?" : '';

$stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM City WHERE zone_id = ? $search_query LIMIT $start, $limit");
$search ? $stmt->execute([$zone_id, "%$search%"]) : $stmt->execute([$zone_id]);
$cities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total rows
$total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$pages = ceil($total / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>City Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>City Management</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $zone['region_id']; ?>"><?php echo htmlspecialchars($zone['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <form class="form-inline mb-3" method="get" action="city.php">
        <input type="hidden" name="zone_id" value="<?php echo htmlspecialchars($zone_id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search Cities" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <a href="city_add.php?zone_id=<?php echo $zone_id; ?>" class="btn btn-primary mb-3">+ Add City</a>
    <?php if (empty($cities)): ?>
        <div class="alert alert-info">There are no cities recorded for this zone. Be the first to add one.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>City Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($cities as $city): ?>
            <tr>
                <td><?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href='subcity.php?city_id=<?php echo $city['city_id']; ?>' class='btn btn-info'>Sub-City</a>
                    <a href='city_view.php?city_id=<?php echo $city['city_id']; ?>' class='btn btn-secondary'>View</a>
                    <a href='city_update.php?city_id=<?php echo $city['city_id']; ?>' class='btn btn-warning'>Update</a>
                    <a href='city_delete.php?city_id=<?php echo $city['city_id']; ?>&zone_id=<?php echo $zone_id; ?>' class='btn btn-danger' onclick='return confirm("Are you sure?")'>Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?zone_id=<?php echo $zone_id; ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
