<?php
require_once './config/session_check.php';
require './config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get user ID from the query string
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $user_id = $_GET['id'];

    // Fetch user details
    $userQuery = $pdo->prepare("SELECT u.username, u.full_name, u.branch_id, b.branch_name, u.status, u.created_at, u.updated_at, u.created_by, u.updated_by 
                                FROM users u 
                                LEFT JOIN branches b ON u.branch_id = b.branch_id 
                                WHERE u.user_id = ?");
    $userQuery->execute([$user_id]);
    $user = $userQuery->fetch(PDO::FETCH_ASSOC);

    // Fetch user role
    $roleQuery = $pdo->prepare("SELECT r.role_name 
                                FROM user_roles ur 
                                LEFT JOIN roles r ON ur.role_id = r.role_id 
                                WHERE ur.user_id = ?");
    $roleQuery->execute([$user_id]);
    $role = $roleQuery->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $createdByQuery = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
        $createdByQuery->execute([$user['created_by']]);
        $createdBy = $createdByQuery->fetch(PDO::FETCH_ASSOC);

        $updatedByQuery = $pdo->prepare("SELECT username FROM users WHERE user_id = ?");
        $updatedByQuery->execute([$user['updated_by']]);
        $updatedBy = $updatedByQuery->fetch(PDO::FETCH_ASSOC);
    }
} else {
    header("Location: user.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">View User</h2>

        <?php if ($user): ?>
            <table class="table table-bordered">
                <tr>
                    <th>Full Name</th>
                    <td><?= htmlspecialchars($user['full_name'] ?? 'null') ?></td>
                </tr>
                <tr>
                    <th>Username</th>
                    <td><?= htmlspecialchars($user['username'] ?? 'null') ?></td>
                </tr>
                <tr>
                    <th>Branch</th>
                    <td><?= htmlspecialchars($user['branch_name'] ?? 'null') ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><?= htmlspecialchars($user['status'] ?? 'null') ?></td>
                </tr>
                <tr>
                    <th>Role</th>
                    <td><?= htmlspecialchars($role['role_name'] ?? 'null') ?></td>
                </tr>
                <tr>
                    <th>Created At</th>
                    <td><?= htmlspecialchars($user['created_at'] ?? 'null') ?></td>
                </tr>
                <tr>
                    <th>Created By</th>
                    <td><?= htmlspecialchars($createdBy['username'] ?? 'null') ?></td>
                </tr>
                <tr>
                    <th>Updated At</th>
                    <td><?= htmlspecialchars($user['updated_at'] ?? 'null') ?></td>
                </tr>
                <tr>
                    <th>Updated By</th>
                    <td><?= htmlspecialchars($updatedBy['username'] ?? 'null') ?></td>
                </tr>
            </table>
            <a href="user.php" class="btn btn-primary">Back to User List</a>
        <?php else: ?>
            <div class="alert alert-danger">User not found.</div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
