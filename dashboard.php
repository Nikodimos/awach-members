<?php
require_once './config/session_check.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$full_name = $_SESSION['full_name'];
$branch_id = $_SESSION['branch_id'];
$status = $_SESSION['status'];
$user_role = $_SESSION['user_role'];

echo "Welcome, " . htmlspecialchars($full_name) . " (" . htmlspecialchars($username) . ")!";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Dashboard</h1>
        <p>Welcome to the SACCO management system, <?php echo htmlspecialchars($full_name); ?>.</p>
        <ul>
            <li>Username: <?php echo htmlspecialchars($username); ?></li>
            <li>Branch ID: <?php echo htmlspecialchars($branch_id); ?></li>
            <li>Status: <?php echo htmlspecialchars($status); ?></li>
            <li>User Role: <?php echo htmlspecialchars($user_role); ?></li>
            <li><a href="./user.php">Users</a></li>
            <li><a href="./Region.php">Region</a></li>
            <li><a href="./Zone.php">Zone</a></li>
            <li><a href="./Subcity.php">Subcity</a></li>
            <li><a href="./Woreda.php">Woreda</a></li>
            <li><a href="./Kebele.php">Kebele</a></li>
            <li><a href="./user.php">Users</a></li>
        </ul>
        <a href="logout.php" class="btn btn-primary">Logout</a>
    </div>
</body>
</html>
