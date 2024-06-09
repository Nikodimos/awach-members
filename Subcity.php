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

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = $search ? "AND sub_city_name LIKE ?" : '';

$stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM SubCity WHERE city_id = ? $search_query LIMIT $start, $limit");
$search ? $stmt->execute([$city_id, "%$search%"]) : $stmt->execute([$city_id]);
$subcities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total rows
$total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$pages = ceil($total / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sub-City Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Sub-City Management</h2>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="region.php">Regions</a></li>
            <li class="breadcrumb-item"><a href="zone.php?region_id=<?php echo $city['region_id']; ?>"><?php echo htmlspecialchars($city['region_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item"><a href="city.php?zone_id=<?php echo $city['zone_id']; ?>"><?php echo htmlspecialchars($city['zone_name'], ENT_QUOTES, 'UTF-8'); ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($city['city_name'], ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>
    <form class="form-inline mb-3" method="get" action="subcity.php">
        <input type="hidden" name="city_id" value="<?php echo htmlspecialchars($city_id, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search Sub-Cities" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <a href="subcity_add.php?city_id=<?php echo $city_id; ?>" class="btn btn-primary mb-3">+ Add Sub-City</a>
    <?php if (empty($subcities)): ?>
        <div class="alert alert-info">There are no sub-cities recorded for this city. Be the first to add one.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Sub-City Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($subcities as $subcity): ?>
            <tr>
                <td><?php echo htmlspecialchars($subcity['sub_city_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href='woreda.php?sub_city_id=<?php echo $subcity['sub_city_id']; ?>' class='btn btn-info'>Woreda</a>
                    <a href='subcity_view.php?sub_city_id=<?php echo $subcity['sub_city_id']; ?>' class='btn btn-secondary'>View</a>
                    <a href='subcity_update.php?sub_city_id=<?php echo $subcity['sub_city_id']; ?>' class='btn btn-warning'>Update</a>
                    <a href='subcity_delete.php?sub_city_id=<?php echo $subcity['sub_city_id']; ?>&city_id=<?php echo $city_id; ?>' class='btn btn-danger' onclick='return confirm("Are you sure?")'>Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?city_id=<?php echo $city_id; ?>&page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
