<?php
require './config/database.php';

// Create tables
$createBranchesTable = "
CREATE TABLE IF NOT EXISTS branches (
    branch_id INT AUTO_INCREMENT PRIMARY KEY,
    branch_name VARCHAR(100) NOT NULL
)";

$createUsersTable = "
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    branch_id INT NOT NULL,
    status ENUM('pending', 'active', 'inactive', 'locked', 'deleted') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(user_id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(user_id) ON DELETE SET NULL
)";

$createRolesTable = "
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL
)";

$createUserRolesTable = "
CREATE TABLE IF NOT EXISTS user_roles (
    user_role_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE
)";

$createAuditTrailTable = "
CREATE TABLE IF NOT EXISTS audit_trail (
    audit_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    target_user_id INT,
    action VARCHAR(50) NOT NULL,
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    details TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";

// Execute table creation
$pdo->exec($createBranchesTable);
$pdo->exec($createUsersTable);
$pdo->exec($createRolesTable);
$pdo->exec($createUserRolesTable);
$pdo->exec($createAuditTrailTable);

// Create default branch
$stmt = $pdo->prepare('INSERT INTO branches (branch_name) VALUES (?)');
$stmt->execute(['Head Office']);

// Create super admin account
$username = 'admin';
$password = password_hash('123456', PASSWORD_BCRYPT);
$full_name = 'Super Admin';
$branch_id = $pdo->lastInsertId(); // Use the ID of the newly created default branch
$status = 'pending';
$created_by = null; // No one created the first admin

$stmt = $pdo->prepare('INSERT INTO users (username, password, full_name, branch_id, status, created_by) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$username, $password, $full_name, $branch_id, $status, $created_by]);

$superAdminId = $pdo->lastInsertId(); // Get the ID of the newly created super admin

// Create admin role
$stmt = $pdo->prepare('INSERT INTO roles (role_name, description) VALUES (?, ?)');
$stmt->execute(['admin', 'Administrator role with full privileges']);

// Assign admin role to super admin
$roleId = $pdo->lastInsertId(); // Get the ID of the newly created admin role
$stmt = $pdo->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)');
$stmt->execute([$superAdminId, $roleId]);

echo "Initialization complete. Super admin account created with username 'admin' and password '123456'.";
?>