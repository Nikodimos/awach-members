<?php
session_start();
require './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=Please fill in all fields.");
        exit;
    }

    try {
        $stmt = $pdo->prepare('SELECT u.user_id, u.username, u.password, u.full_name, u.branch_id, u.status, ur.role_id FROM users u LEFT JOIN user_roles ur ON u.user_id = ur.user_id WHERE u.username = ?');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'pending') {
                // Redirect to password change page
                $_SESSION['user_id'] = $user['user_id'];
                header("Location: change_password.php");
                exit;
            } elseif ($user['status'] === 'active') {
                // Set session variables and redirect to dashboard
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['branch_id'] = $user['branch_id'];
                $_SESSION['status'] = $user['status'];
                $_SESSION['user_role'] = $user['role_id'];
                header("Location: dashboard.php");
                exit;
            } else {
                header("Location: login.php?error=Account is not active.");
                exit;
            }
        } else {
            header("Location: login.php?error=Invalid username or password.");
            exit;
        }
    } catch (PDOException $e) {
        echo "An error occurred: " . $e->getMessage();
    }
} else {
    header("Location: login.php");
    exit;
}
?>