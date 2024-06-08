<?php
require './config/database.php';

echo '<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    .alert { padding: 10px; margin-bottom: 15px; border: 1px solid transparent; border-radius: 4px; }
    .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
    .alert-info { color: #0c5460; background-color: #d1ecf1; border-color: #bee5eb; }
    .alert-warning { color: #856404; background-color: #fff3cd; border-color: #ffeeba; }
    .btn { display: inline-block; font-weight: 400; color: #212529; text-align: center; vertical-align: middle; user-select: none; background-color: #007bff; border: 1px solid #007bff; padding: .375rem .75rem; font-size: 1rem; line-height: 1.5; border-radius: .25rem; text-decoration: none; color: #fff; }
</style>';

try {
    // Attempt to connect to the database
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<div class="alert alert-success">Successfully connected to the database.</div>';
} catch (PDOException $e) {
    die('<div class="alert alert-warning">Failed to connect to the database: ' . $e->getMessage() . '</div>');
}

// Create tables
$createBranchesTable = "
CREATE TABLE IF NOT EXISTS branches (
    branch_id INT AUTO_INCREMENT PRIMARY KEY,
    branch_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createBranchesTable);
echo '<div class="alert alert-success">Branches Table created.</div>';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createUsersTable);
echo '<div class="alert alert-success">Users Table created.</div>';

$createRolesTable = "
CREATE TABLE IF NOT EXISTS roles (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE,
    description VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createRolesTable);
echo '<div class="alert alert-success">Roles Table created.</div>';

$createUserRolesTable = "
CREATE TABLE IF NOT EXISTS user_roles (
    user_role_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(role_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createUserRolesTable);
echo '<div class="alert alert-success">User Roles Table created.</div>';

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createAuditTrailTable);
echo '<div class="alert alert-success">Audit Trail Table created.</div>';

$createRegionsTable = "
CREATE TABLE IF NOT EXISTS Region (
    region_id INT AUTO_INCREMENT PRIMARY KEY,
    region_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createRegionsTable);
echo '<div class="alert alert-success">Region Table created.</div>';

$createZonesTable = "
CREATE TABLE IF NOT EXISTS Zone (
    zone_id INT AUTO_INCREMENT PRIMARY KEY,
    region_id INT NOT NULL,
    zone_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (region_id) REFERENCES Region(region_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createZonesTable);
echo '<div class="alert alert-success">Zone Table created.</div>';

$createCitiesTable = "
CREATE TABLE IF NOT EXISTS City (
    city_id INT AUTO_INCREMENT PRIMARY KEY,
    zone_id INT NOT NULL,
    city_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (zone_id) REFERENCES Zone(zone_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createCitiesTable);
echo '<div class="alert alert-success">City Table created.</div>';

$createSubCitiesTable = "
CREATE TABLE IF NOT EXISTS SubCity (
    sub_city_id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    sub_city_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (city_id) REFERENCES City(city_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createSubCitiesTable);
echo '<div class="alert alert-success">SubCity Table created.</div>';

$createWoredasTable = "
CREATE TABLE IF NOT EXISTS Woreda (
    woreda_id INT AUTO_INCREMENT PRIMARY KEY,
    sub_city_id INT NOT NULL,
    woreda_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (sub_city_id) REFERENCES SubCity(sub_city_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createWoredasTable);
echo '<div class="alert alert-success">Woreda Table created.</div>';

$createKebelesTable = "
CREATE TABLE IF NOT EXISTS Kebele (
    kebele_id INT AUTO_INCREMENT PRIMARY KEY,
    woreda_id INT NOT NULL,
    kebele_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (woreda_id) REFERENCES Woreda(woreda_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createKebelesTable);
echo '<div class="alert alert-success">Kebele Table created.</div>';

$createTitlesTable = "
CREATE TABLE IF NOT EXISTS Title (
    title_id INT AUTO_INCREMENT PRIMARY KEY,
    title_name VARCHAR(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createTitlesTable);
echo '<div class="alert alert-success">Title Table created.</div>';

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
    gender VARCHAR(10) NOT NULL,
    nationality VARCHAR(50) NOT NULL,
    marital_status VARCHAR(10) NOT NULL,
    region_id INT NOT NULL,
    zone_id INT NOT NULL,
    city_id INT NOT NULL,
    sub_city_id INT NOT NULL,
    woreda_id INT NOT NULL,
    kebele_id INT NOT NULL,
    house_number VARCHAR(100) NOT NULL,
    email_address VARCHAR(100),
    mobile_numbers VARCHAR(255),
    mobile_number_sms VARCHAR(20),
    education_level VARCHAR(20) NOT NULL,
    profession VARCHAR(100),
    occupation VARCHAR(100),
    monthly_income DECIMAL(10, 2),
    family_size_male INT,
    family_size_female INT,
    disability BOOLEAN,
    regular_saving_payment_mode VARCHAR(50),
    saving_account_type VARCHAR(50),
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
    FOREIGN KEY (city_id) REFERENCES City(city_id) ON DELETE CASCADE,
    FOREIGN KEY (sub_city_id) REFERENCES SubCity(sub_city_id) ON DELETE CASCADE,
    FOREIGN KEY (woreda_id) REFERENCES Woreda(woreda_id) ON DELETE CASCADE,
    FOREIGN KEY (kebele_id) REFERENCES Kebele(kebele_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createMembersTable);
echo '<div class="alert alert-success">Members Table created.</div>';

$createMemberProfilePictureTable = "
CREATE TABLE IF NOT EXISTS MemberProfilePicture (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (member_id) REFERENCES Members(recID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createMemberProfilePictureTable);
echo '<div class="alert alert-success">Member Profile Picture Table created.</div>';

$createMemberIDCardPhotoTable = "
CREATE TABLE IF NOT EXISTS MemberIDCardPhoto (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (member_id) REFERENCES Members(recID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createMemberIDCardPhotoTable);
echo '<div class="alert alert-success">Member ID Card Photo Table created.</div>';

$createMemberScannedDocumentTable = "
CREATE TABLE IF NOT EXISTS MemberScannedDocument (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (member_id) REFERENCES Members(recID) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$pdo->exec($createMemberScannedDocumentTable);
echo '<div class="alert alert-success">Member Scanned Document Table created.</div>';

// Create default branch
$stmt = $pdo->prepare('INSERT INTO branches (branch_name) VALUES (?)');
$stmt->execute(['Head Office']);
echo '<div class="alert alert-info">Default branch "Head Office" created.</div>';

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
echo '<div class="alert alert-info">Admin role created.</div>';

// Assign admin role to super admin
$roleId = $pdo->lastInsertId(); // Get the ID of the newly created admin role
$stmt = $pdo->prepare('INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)');
$stmt->execute([$superAdminId, $roleId]);
echo '<div class="alert alert-info">Super admin account created with username "admin" and password "123456".</div>';

echo '<div class="alert alert-success">Initialization complete. <a href="index.php" class="btn">Get Started</a></div>';
?>
