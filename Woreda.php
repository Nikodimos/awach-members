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

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = $search ? "AND woreda_name LIKE ?" : '';

$stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM Woreda WHERE sub_city_id = ? $search_query LIMIT $start, $limit");
$search ? $stmt->execute([$sub_city_id, "%$search%"]) : $stmt->execute([$sub_city_id]);
$woredas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total rows
$total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$pages = ceil($total / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Woreda Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Woreda Management</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $sub_city['region_id']; ?>"><?php echo htmlspecialchars($sub_city['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $sub_city['zone_id']; ?>"><?php echo htmlspecialchars($sub_city['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="subcity.php?city_id=<?php echo $sub_city['city_id']; ?>"><?php echo htmlspecialchars($sub_city['city_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($sub_city['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <form class="form-inline mb-3" method="get" action="woreda.php">
        <input type="hidden" name="sub_city_id" value="<?php echo htmlspecialchars($sub_city_id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search Woredas" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <a href="woreda_add.php?sub_city_id=<?php echo $sub_city_id; ?>" class="btn btn-primary mb-3">+ Add Woreda</a>
    <?php if (empty($woredas)): ?>
        <div class="alert alert-info">There are no woredas recorded for this sub-city. Be the first to add one.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Woreda Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($woredas as $woreda): ?>
            <tr>
                <td><?php echo htmlspecialchars($woreda['woreda_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href='kebele.php?woreda_id=<?php echo $woreda['woreda_id']; ?>' class='btn btn-info'>Kebele</a>
                    <a href='woreda_view.php?woreda_id=<?php echo $woreda['woreda_id']; ?>' class='btn btn-secondary'>View</a>
                    <a href='woreda_update.php?woreda_id=<?php echo $woreda['woreda_id']; ?>' class='btn btn-warning'>Update</a>
                    <a href='woreda_delete.php?woreda_id=<?php echo $woreda['woreda_id']; ?>&sub_city_id=<?php echo $sub_city_id; ?>' class='btn btn-danger' onclick='return confirm("Are you sure?")'>Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?sub_city_id=<?php echo $sub_city_id; ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
