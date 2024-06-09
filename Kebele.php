<?php
require_once './config/session_check.php';
require './config/database.php';

if (!isset($_GET['woreda_id'])) {
    header('Location: woreda.php');
    exit;
}

$woreda_id = $_GET['woreda_id'];

// Fetch the woreda, sub-city, city, zone, and region name for breadcrumb
$woreda_stmt = $pdo->prepare('SELECT w.woreda_name, sc.sub_city_name, c.city_name, z.zone_name, r.region_name, sc.sub_city_id, c.city_id, z.zone_id, r.region_id FROM Woreda w JOIN SubCity sc ON w.sub_city_id = sc.sub_city_id JOIN City c ON sc.city_id = c.city_id JOIN Zone z ON c.zone_id = z.zone_id JOIN Region r ON z.region_id = r.region_id WHERE w.woreda_id = ?');
$woreda_stmt->execute([$woreda_id]);
$woreda = $woreda_stmt->fetch(PDO::FETCH_ASSOC);

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = $search ? "AND kebele_name LIKE ?" : '';

$stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM Kebele WHERE woreda_id = ? $search_query LIMIT $start, $limit");
$search ? $stmt->execute([$woreda_id, "%$search%"]) : $stmt->execute([$woreda_id]);
$kebeles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total rows
$total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$pages = ceil($total / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kebele Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Kebele Management</h2>
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
    <form class="form-inline mb-3" method="get" action="kebele.php">
        <input type="hidden" name="woreda_id" value="<?php echo htmlspecialchars($woreda_id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search Kebeles" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <a href="kebele_add.php?woreda_id=<?php echo $woreda_id; ?>" class="btn btn-primary mb-3">+ Add Kebele</a>
    <?php if (empty($kebeles)): ?>
        <div class="alert alert-info">There are no kebeles recorded for this woreda. Be the first to add one.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Kebele Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($kebeles as $kebele): ?>
            <tr>
                <td><?php echo htmlspecialchars($kebele['kebele_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href='kebele_view.php?kebele_id=<?php echo $kebele['kebele_id']; ?>' class='btn btn-secondary'>View</a>
                    <a href='kebele_update.php?kebele_id=<?php echo $kebele['kebele_id']; ?>' class='btn btn-warning'>Update</a>
                    <a href='kebele_delete.php?kebele_id=<?php echo $kebele['kebele_id']; ?>&woreda_id=<?php echo $woreda_id; ?>' class='btn btn-danger' onclick='return confirm("Are you sure?")'>Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?woreda_id=<?php echo $woreda_id; ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>