<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['region_id'])) {
    header('Location: region.php');
    exit;
}

$region_id = $_GET['region_id'];

// Fetch the region name for breadcrumb
$region_stmt = $pdo->prepare('SELECT region_name FROM Region WHERE region_id = ?');
$region_stmt->execute([$region_id]);
$region = $region_stmt->fetch(PDO::FETCH_ASSOC);

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = $search ? "AND zone_name LIKE ?" : '';

$stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM Zone WHERE region_id = ? $search_query LIMIT $start, $limit");
$search ? $stmt->execute([$region_id, "%$search%"]) : $stmt->execute([$region_id]);
$zones = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total rows
$total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$pages = ceil($total / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Zone Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Zone Management</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($region['region_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <form class="form-inline mb-3" method="get" action="zone.php">
        <input type="hidden" name="region_id" value="<?php echo htmlspecialchars($region_id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search Zones" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <a href="zone_add.php?region_id=<?php echo $region_id; ?>" class="btn btn-primary mb-3">+ Add Zone</a>
    <?php if (empty($zones)): ?>
        <div class="alert alert-info">There are no zones recorded for this region. Be the first to add one.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Zone Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($zones as $zone): ?>
            <tr>
                <td><?php echo htmlspecialchars($zone['zone_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href='city.php?zone_id=<?php echo $zone['zone_id']; ?>' class='btn btn-info'>City</a>
                    <a href='zone_view.php?zone_id=<?php echo $zone['zone_id']; ?>' class='btn btn-secondary'>View</a>
                    <a href='zone_update.php?zone_id=<?php echo $zone['zone_id']; ?>' class='btn btn-warning'>Update</a>
                    <a href='zone_delete.php?zone_id=<?php echo $zone['zone_id']; ?>&region_id=<?php echo $region_id; ?>' class='btn btn-danger' onclick='return confirm("Are you sure?")'>Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?region_id=<?php echo $region_id; ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
