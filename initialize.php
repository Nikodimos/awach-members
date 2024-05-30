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
    old_value TEXT,
    new_value TEXT,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (target_user_id) REFERENCES users(user_id) ON DELETE CASCADE
)";

// Create Region, Zone, SubCity, Woreda, and Kebele tables
$createRegionTable = "
CREATE TABLE IF NOT EXISTS Region (
    region_id INT AUTO_INCREMENT PRIMARY KEY,
    region_name VARCHAR(100) NOT NULL,
    UNIQUE(region_name)
)";

$createZoneTable = "
CREATE TABLE IF NOT EXISTS Zone (
    zone_id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    zone_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (region_id) REFERENCES Region(region_id) ON DELETE CASCADE,
    UNIQUE(region_id, zone_name)
)";

$createSubCityTable = "
CREATE TABLE IF NOT EXISTS SubCity (
    sub_city_id INT AUTO_INCREMENT PRIMARY KEY,
    zone_id INT NOT NULL,
    sub_city_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (zone_id) REFERENCES Zone(zone_id) ON DELETE CASCADE,
    UNIQUE(zone_id, sub_city_name)
)";

$createWoredaTable = "
CREATE TABLE IF NOT EXISTS Woreda (
    woreda_id INT AUTO_INCREMENT PRIMARY KEY,
    sub_city_id INT NOT NULL,
    woreda_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (sub_city_id) REFERENCES SubCity(sub_city_id) ON DELETE CASCADE,
    UNIQUE(sub_city_id, woreda_name)
)";

$createKebeleTable = "
CREATE TABLE IF NOT EXISTS Kebele (
    kebele_id INT AUTO_INCREMENT PRIMARY KEY,
    woreda_id INT NOT NULL,
    kebele_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (woreda_id) REFERENCES Woreda(woreda_id) ON DELETE CASCADE,
    UNIQUE(woreda_id, kebele_name)
)";

$createTitleTable = "
CREATE TABLE IF NOT EXISTS Title (
    title_id INT AUTO_INCREMENT PRIMARY KEY,
    title_name VARCHAR(10) NOT NULL,
    UNIQUE(title_name)
)";

$createMembersTable = "
CREATE TABLE IF NOT EXISTS Members (
    recID INT AUTO_INCREMENT PRIMARY KEY,
    branch_id INT NOT NULL,
    opening_date DATE NOT NULL,
    member_id VARCHAR(50) NOT NULL,
    title_id INT NOT NULL,
    member_name VARCHAR(100) NOT NULL,
    member_father_name VARCHAR(100) NOT NULL,
    member_grandfather_name VARCHAR(100) NOT NULL,
    member_mother_name VARCHAR(100) NOT NULL,
    member_mother_father_name VARCHAR(100) NOT NULL,
    member_mother_grandfather_name VARCHAR(100) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    nationality VARCHAR(50) NOT NULL,
    marital_status ENUM('Single', 'Married', 'Divorced', 'Widowed') NOT NULL,
    region_id INT NOT NULL,
    zone_id INT NOT NULL,
    sub_city_id INT NOT NULL,
    woreda_id INT NOT NULL,
    kebele_id INT NOT NULL,
    email_address VARCHAR(100),
    mobile_numbers VARCHAR(255),
    mobile_number_sms VARCHAR(20),
    education_level ENUM('None', 'Primary', 'Secondary', 'Diploma', 'Bachelor', 'Master', 'Doctorate') NOT NULL,
    profession VARCHAR(100),
    occupation VARCHAR(100),
    monthly_income DECIMAL(10, 2),
    family_size_male INT,
    family_size_female INT,
    disability BOOLEAN,
    regular_saving_payment_mode BOOLEAN,
    saving_account_type BOOLEAN,
    service_type VARCHAR(50),
    source_of_info VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by INT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE CASCADE,
    FOREIGN KEY (title_id) REFERENCES Title(title_id) ON DELETE CASCADE,
    FOREIGN KEY (region_id) REFERENCES Region(region_id) ON DELETE CASCADE,
    FOREIGN KEY (zone_id) REFERENCES Zone(zone_id) ON DELETE CASCADE,
    FOREIGN KEY (sub_city_id) REFERENCES SubCity(sub_city_id) ON DELETE CASCADE,
    FOREIGN KEY (woreda_id) REFERENCES Woreda(woreda_id) ON DELETE CASCADE,
    FOREIGN KEY (kebele_id) REFERENCES Kebele(kebele_id) ON DELETE CASCADE
)";

// Create tables for file storage
$createMemberProfilePictureTable = "
CREATE TABLE IF NOT EXISTS MemberProfilePicture (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (member_id) REFERENCES Members(recID) ON DELETE CASCADE
)";

$createMemberIDCardPhotoTable = "
CREATE TABLE IF NOT EXISTS MemberIDCardPhoto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (member_id) REFERENCES Members(recID) ON DELETE CASCADE
)";

$createMemberScannedDocumentTable = "
CREATE TABLE IF NOT EXISTS MemberScannedDocument (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (member_id) REFERENCES Members(recID) ON DELETE CASCADE
)";

// Execute table creation
$pdo->exec($createBranchesTable);
$pdo->exec($createUsersTable);
$pdo->exec($createRolesTable);
$pdo->exec($createUserRolesTable);
$pdo->exec($createAuditTrailTable);

$pdo->exec($createRegionTable);
$pdo->exec($createZoneTable);
$pdo->exec($createSubCityTable);
$pdo->exec($createWoredaTable);
$pdo->exec($createKebeleTable);
$pdo->exec($createTitleTable);
$pdo->exec($createMembersTable);

$pdo->exec($createMemberProfilePictureTable);
$pdo->exec($createMemberIDCardPhotoTable);
$pdo->exec($createMemberScannedDocumentTable);

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