<?php
require_once './config/session_check.php';
require './config/database.php';


// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch users with optional search/filter
$searchQuery = "SELECT u.user_id, u.full_name, u.username, b.branch_name, r.role_name 
                FROM users u 
                JOIN branches b ON u.branch_id = b.branch_id 
                JOIN user_roles ur ON u.user_id = ur.user_id 
                JOIN roles r ON ur.role_id = r.role_id 
                WHERE 1=1";

$params = [];
if (!empty($_GET['full_name'])) {
    $searchQuery .= " AND u.full_name LIKE ?";
    $params[] = '%' . $_GET['full_name'] . '%';
}
if (!empty($_GET['username'])) {
    $searchQuery .= " AND u.username LIKE ?";
    $params[] = '%' . $_GET['username'] . '%';
}
if (!empty($_GET['branch_id'])) {
    $searchQuery .= " AND u.branch_id = ?";
    $params[] = $_GET['branch_id'];
}
if (!empty($_GET['role_id'])) {
    $searchQuery .= " AND ur.role_id = ?";
    $params[] = $_GET['role_id'];
}

$stmt = $pdo->prepare($searchQuery);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch branches for dropdown
$branchesQuery = $pdo->query("SELECT branch_id, branch_name FROM branches");
$branches = $branchesQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch roles for dropdown
$rolesQuery = $pdo->query("SELECT role_id, role_name FROM roles");
$roles = $rolesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">User Management</h2>

        <!-- Add User Button -->
        <button class="btn btn-primary mb-4" data-toggle="modal" data-target="#addUserModal">Add User</button>

        <!-- Filter Form -->
        <form method="get" class="mb-4">
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" class="form-control" id="full_name" value="<?= htmlspecialchars($_GET['full_name'] ?? '') ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="username">Username</label>
                    <input type="text" name="username" class="form-control" id="username" value="<?= htmlspecialchars($_GET['username'] ?? '') ?>">
                </div>
                <div class="form-group col-md-3">
                    <label for="branch_id">Branch</label>
                    <select name="branch_id" class="form-control" id="branch_id">
                        <option value="">All Branches</option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= htmlspecialchars($branch['branch_id']) ?>" <?= ($_GET['branch_id'] ?? '') == $branch['branch_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($branch['branch_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="role_id">Role</label>
                    <select name="role_id" class="form-control" id="role_id">
                        <option value="">All Roles</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= htmlspecialchars($role['role_id']) ?>" <?= ($_GET['role_id'] ?? '') == $role['role_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($role['role_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <!-- Users Table -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Username</th>
                    <th>Branch Name</th>
                    <th>Role Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['full_name']) ?></td>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['branch_name']) ?></td>
                        <td><?= htmlspecialchars($user['role_name']) ?></td>
                        <td>
                            <a href="view_user.php?id=<?= $user['user_id'] ?>" class="btn btn-info btn-sm">View</a>
                            <a href="update_user.php?id=<?= $user['user_id'] ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="reset_user.php?id=<?= $user['user_id'] ?>" class="btn btn-danger btn-sm">Reset</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm" method="post" action="add_user.php">
                        <div class="form-group">
                            <label for="add_full_name">Full Name</label>
                            <input type="text" name="full_name" class="form-control" id="add_full_name" required>
                        </div>
                        <div class="form-group">
                            <label for="add_username">Username</label>
                            <input type="text" name="username" class="form-control" id="add_username" required>
                        </div>
                        <div class="form-group">
                            <label for="add_branch_id">Branch</label>
                            <select name="branch_id" class="form-control" id="add_branch_id" required>
                                <?php foreach ($branches as $branch): ?>
                                    <option value="<?= htmlspecialchars($branch['branch_id']) ?>">
                                        <?= htmlspecialchars($branch['branch_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_role_id">Role</label>
                            <select name="role_id" class="form-control" id="add_role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= htmlspecialchars($role['role_id']) ?>">
                                        <?= htmlspecialchars($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div id="error-message" class="text-danger"></div>
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function validateAddUserForm() {
            var fullName = document.getElementById('add_full_name').value.trim();
            var username = document.getElementById('add_username').value.trim();
            var branchId = document.getElementById('add_branch_id').value;
            var roleId = document.getElementById('add_role_id').value;
            var errorMessage = document.getElementById('error-message');

            if (fullName === '' || username === '' || branchId === '' || roleId === '') {
                errorMessage.textContent = 'All fields are required.';
                return false;
            }

            return true;
        }

        document.getElementById('addUserForm').onsubmit = function() {
            return validateAddUserForm();
        };
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
