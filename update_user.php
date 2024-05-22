<?php
require_once './config/session_check.php';
require './config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    echo "User ID is required.";
    exit;
}

$user_id = $_GET['id'];

// Fetch user details
$userQuery = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$userQuery->execute([$user_id]);
$user = $userQuery->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch branches for dropdown
$branchesQuery = $pdo->query("SELECT branch_id, branch_name FROM branches");
$branches = $branchesQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch roles for dropdown
$currentRoleQuery = $pdo->prepare("SELECT role_id FROM user_roles WHERE user_id = ?");
$currentRoleQuery->execute([$user_id]);
$currentRole = $currentRoleQuery->fetch(PDO::FETCH_ASSOC);

$rolesQuery = $pdo->query("SELECT role_id, role_name FROM roles");
$roles = $rolesQuery->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $branch_id = $_POST['branch_id'];
    $role_id = $_POST['role_id'];
    $status = $_POST['status'];
    $updated_by = $_SESSION['user_id'];
    $updated_at = date('Y-m-d H:i:s');

    // Update user details
    $updateUserQuery = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, branch_id = ?, status = ?, updated_at = ?, updated_by = ? WHERE user_id = ?");
    $updateUserQuery->execute([$full_name, $username, $branch_id, $status, $updated_at, $updated_by, $user_id]);

    // Update user role
    $updateRoleQuery = $pdo->prepare("UPDATE user_roles SET role_id = ? WHERE user_id = ?");
    $updateRoleQuery->execute([$role_id, $user_id]);

    // Record in audit trail
    $details = json_encode([
        'full_name' => $full_name,
        'username' => $username,
        'branch_id' => $branch_id,
        'role_id' => $role_id,
        'status' => $status
    ]);
    $auditQuery = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, timestamp, details) VALUES (?, ?, ?, ?, ?)");
    $auditQuery->execute([$updated_by, 'User updated', $user_id, $updated_at, $details]);

    header("Location: view_user.php?id=$user_id");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Update User</h2>

        <form method="post">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" class="form-control" id="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>
            <div class="form-group">
                <label for="branch_id">Branch</label>
                <select name="branch_id" class="form-control" id="branch_id" required>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= htmlspecialchars($branch['branch_id']) ?>" <?= $branch['branch_id'] == $user['branch_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($branch['branch_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="role_id">Role</label>
                <select name="role_id" class="form-control" id="role_id" required>
                    <?php foreach ($roles as $role): ?>
                        <option value="<?= htmlspecialchars($role['role_id']) ?>" <?= isset($currentRole['role_id']) && $role['role_id'] == $currentRole['role_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($role['role_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <div>
                    <label>
                        <input type="radio" name="status" value="pending" <?= $user['status'] == 'pending' ? 'checked' : '' ?>> Pending
                    </label>
                    <label>
                        <input type="radio" name="status" value="inactive" <?= $user['status'] == 'inactive' ? 'checked' : '' ?>> Inactive
                    </label>
                    <label>
                        <input type="radio" name="status" value="locked" <?= $user['status'] == 'locked' ? 'checked' : '' ?>> Locked
                    </label>
                    <label>
                        <input type="radio" name="status" value="deleted" <?= $user['status'] == 'deleted' ? 'checked' : '' ?>> Deleted
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="view_user.php?id=<?= $user_id ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
