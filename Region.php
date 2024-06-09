<?php
require_once './config/session_check.php';
require './config/database.php';

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// Search
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$search_query = $search ? "WHERE region_name LIKE ?" : '';

$stmt = $pdo->prepare("SELECT SQL_CALC_FOUND_ROWS * FROM Region $search_query LIMIT $start, $limit");
$search ? $stmt->execute(["%$search%"]) : $stmt->execute();
$regions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total rows
$total = $pdo->query("SELECT FOUND_ROWS()")->fetchColumn();
$pages = ceil($total / $limit);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Region Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Region Management</h2>
    <form class="form-inline mb-3" method="get" action="region.php">
        <input type="text" name="search" class="form-control mr-sm-2" placeholder="Search Regions" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>
    <a href="region_add.php" class="btn btn-primary mb-3">+ Add Region</a>
    <?php if (empty($regions)): ?>
        <div class="alert alert-info">There are no regions recorded. Be the first to add one.</div>
    <?php else: ?>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>Region Name</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($regions as $region): ?>
            <tr>
                <td><?php echo htmlspecialchars($region['region_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                <td>
                    <a href='zone.php?region_id=<?php echo $region['region_id']; ?>' class='btn btn-info'>Zones</a>
                    <a href='region_view.php?region_id=<?php echo $region['region_id']; ?>' class='btn btn-secondary'>View</a>
                    <a href='region_update.php?region_id=<?php echo $region['region_id']; ?>' class='btn btn-warning'>Update</a>
                    <a href='region_delete.php?region_id=<?php echo $region['region_id']; ?>' class='btn btn-danger' onclick='return confirm("Are you sure?")'>Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $pages; $i++): ?>
                <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
</div>
</body>
</html>
