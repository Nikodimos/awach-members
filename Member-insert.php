<?php
require_once './config/session_check.php';
require './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $branch_id = $_POST['branch_id'];
    $opening_date = $_POST['opening_date'];
    $member_id = trim($_POST['member_id']);
    $title_id = $_POST['title_id'];
    $member_name = trim($_POST['member_name']);
    $member_father_name = trim($_POST['member_father_name']);
    $member_grandfather_name = trim($_POST['member_grandfather_name']);
    $member_mother_name = trim($_POST['member_mother_name']);
    $member_mother_father_name = trim($_POST['member_mother_father_name']);
    $member_mother_grandfather_name = trim($_POST['member_mother_grandfather_name']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $nationality = trim($_POST['nationality']);
    $marital_status = $_POST['marital_status'];
    $region_id = $_POST['region_id'];
    $zone_id = $_POST['zone_id'];
    $sub_city_id = $_POST['sub_city_id'];
    $woreda_id = $_POST['woreda_id'];
    $kebele_id = $_POST['kebele_id'];
    $email_address = trim($_POST['email_address']);
    $mobile_numbers = array_filter($_POST['mobile_numbers'], function($num) { return !empty($num); });
    $mobile_number_sms = trim($_POST['mobile_number_sms']);
    $education_level = $_POST['education_level'];
    $profession = trim($_POST['profession']);
    $occupation = trim($_POST['occupation']);
    $monthly_income = $_POST['monthly_income'];
    $family_size_male = $_POST['family_size_male'];
    $family_size_female = $_POST['family_size_female'];
    $disability = $_POST['disability'];
    $regular_saving_payment_mode = $_POST['regular_saving_payment_mode'];
    $saving_account_type = $_POST['saving_account_type'];
    $service_type = $_POST['service_type'];
    $source_of_info = trim($_POST['source_of_info']);
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session

    // Validate mandatory fields
    if (empty($branch_id) || empty($opening_date) || empty($member_id) || empty($title_id) || empty($member_name) ||
        empty($member_father_name) || empty($member_grandfather_name) || empty($date_of_birth) || empty($gender) ||
        empty($nationality) || empty($marital_status) || empty($region_id) || empty($zone_id) || empty($sub_city_id) ||
        empty($woreda_id) || empty($kebele_id) || empty($education_level)) {
        echo "All mandatory fields are required.";
        exit;
    }

    // Validate member ID
    if (strlen($member_id) != 10 || $member_id[0] != '2') {
        echo "Member ID must be 10 characters long and start with '2'.";
        exit;
    }

    // Validate mobile numbers
    foreach ($mobile_numbers as $mobile_number) {
        if (!preg_match('/^\d{10}$/', $mobile_number)) {
            echo "Mobile number must be 10 digits.";
            exit;
        }
    }
    if (!preg_match('/^\d{10}$/', $mobile_number_sms)) {
        echo "Mobile number for SMS must be 10 digits.";
        exit;
    }

    // Check for duplicate member ID
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM Members WHERE member_id = ?");
    $stmt->execute([$member_id]);
    if ($stmt->fetchColumn() > 0) {
        echo "Member ID already exists.";
        exit;
    }

    // Insert new member
    $stmt = $pdo->prepare("
        INSERT INTO Members (
            branch_id, opening_date, member_id, title_id, member_name, member_father_name, member_grandfather_name,
            member_mother_name, member_mother_father_name, member_mother_grandfather_name, date_of_birth, gender,
            nationality, marital_status, region_id, zone_id, sub_city_id, woreda_id, kebele_id, email_address,
            mobile_numbers, mobile_number_sms, education_level, profession, occupation, monthly_income,
            family_size_male, family_size_female, disability, regular_saving_payment_mode, saving_account_type,
            service_type, source_of_info, created_by, created_at, updated_by, updated_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, NOW())
    ");
    $stmt->execute([
        $branch_id, $opening_date, $member_id, $title_id, $member_name, $member_father_name, $member_grandfather_name,
        $member_mother_name, $member_mother_father_name, $member_mother_grandfather_name, $date_of_birth, $gender,
        $nationality, $marital_status, $region_id, $zone_id, $sub_city_id, $woreda_id, $kebele_id, $email_address,
        implode(',', $mobile_numbers), $mobile_number_sms, $education_level, $profession, $occupation, $monthly_income,
        $family_size_male, $family_size_female, $disability, $regular_saving_payment_mode, $saving_account_type,
        $service_type, $source_of_info, $user_id, $user_id
    ]);

    // Record in audit trail
    $member_rec_id = $pdo->lastInsertId();
    $details = json_encode([
        'branch_id' => $branch_id,
        'opening_date' => $opening_date,
        'member_id' => $member_id,
        'title_id' => $title_id,
        'member_name' => $member_name,
        'member_father_name' => $member_father_name,
        'member_grandfather_name' => $member_grandfather_name,
        'member_mother_name' => $member_mother_name,
        'member_mother_father_name' => $member_mother_father_name,
        'member_mother_grandfather_name' => $member_mother_grandfather_name,
        'date_of_birth' => $date_of_birth,
        'gender' => $gender,
        'nationality' => $nationality,
        'marital_status' => $marital_status,
        'region_id' => $region_id,
        'zone_id' => $zone_id,
        'sub_city_id' => $sub_city_id,
        'woreda_id' => $woreda_id,
        'kebele_id' => $kebele_id,
        'email_address' => $email_address,
        'mobile_numbers' => implode(',', $mobile_numbers),
        'mobile_number_sms' => $mobile_number_sms,
        'education_level' => $education_level,
        'profession' => $profession,
        'occupation' => $occupation,
        'monthly_income' => $monthly_income,
        'family_size_male' => $family_size_male,
        'family_size_female' => $family_size_female,
        'disability' => $disability,
        'regular_saving_payment_mode' => $regular_saving_payment_mode,
        'saving_account_type' => $saving_account_type,
        'service_type' => $service_type,
        'source_of_info' => $source_of_info,
        'created_by' => $user_id
    ]);
    $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, target_user_id, details) VALUES (?, 'insert', ?, ?)");
    $audit_stmt->execute([$user_id, $member_rec_id, $details]);

    echo "Member added successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Insert Member</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#region_id').change(function() {
                var region_id = $(this).val();
                $.ajax({
                    url: 'get_zones.php',
                    type: 'POST',
                    data: {region_id: region_id},
                    success: function(data) {
                        $('#zone_id').html(data);
                    }
                });
            });

            $('#zone_id').change(function() {
                var zone_id = $(this).val();
                $.ajax({
                    url: 'get_sub_cities.php',
                    type: 'POST',
                    data: {zone_id: zone_id},
                    success: function(data) {
                        $('#sub_city_id').html(data);
                    }
                });
            });

            $('#sub_city_id').change(function() {
                var sub_city_id = $(this).val();
                $.ajax({
                    url: 'get_woredas.php',
                    type: 'POST',
                    data: {sub_city_id: sub_city_id},
                    success: function(data) {
                        $('#woreda_id').html(data);
                    }
                });
            });

            $('#woreda_id').change(function() {
                var woreda_id = $(this).val();
                $.ajax({
                    url: 'get_kebeles.php',
                    type: 'POST',
                    data: {woreda_id: woreda_id},
                    success: function(data) {
                        $('#kebele_id').html(data);
                    }
                });
            });

            $('#add_mobile_number').click(function() {
                $('#mobile_numbers_container').append('<div class="form-group"><input type="text" name="mobile_numbers[]" class="form-control" placeholder="Additional Mobile Number"></div>');
            });
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h2>Insert Member</h2>
        <form method="post" action="Member-insert.php">
            <div class="form-group">
                <label for="branch_id">Branch</label>
                <select name="branch_id" id="branch_id" class="form-control" required>
                    <!-- Populate with branches from the database -->
                    <?php
                    $stmt = $pdo->query("SELECT * FROM branches");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value=\"{$row['branch_id']}\">{$row['branch_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="opening_date">Opening Date</label>
                <input type="date" name="opening_date" id="opening_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="member_id">Member ID</label>
                <input type="text" name="member_id" id="member_id" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="title_id">Title</label>
                <select name="title_id" id="title_id" class="form-control" required>
                    <!-- Populate with titles from the database -->
                    <?php
                    $stmt = $pdo->query("SELECT * FROM Title");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value=\"{$row['title_id']}\">{$row['title_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="member_name">Member Name</label>
                <input type="text" name="member_name" id="member_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="member_father_name">Father's Name</label>
                <input type="text" name="member_father_name" id="member_father_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="member_grandfather_name">Grandfather's Name</label>
                <input type="text" name="member_grandfather_name" id="member_grandfather_name" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="member_mother_name">Mother's Name</label>
                <input type="text" name="member_mother_name" id="member_mother_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="member_mother_father_name">Mother's Father's Name</label>
                <input type="text" name="member_mother_father_name" id="member_mother_father_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="member_mother_grandfather_name">Mother's Grandfather's Name</label>
                <input type="text" name="member_mother_grandfather_name" id="member_mother_grandfather_name" class="form-control">
            </div>
            <div class="form-group">
                <label for="date_of_birth">Date of Birth</label>
                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="gender">Gender</label>
                <div>
                    <input type="radio" name="gender" id="gender_male" value="Male" required> <label for="gender_male">Male</label>
                    <input type="radio" name="gender" id="gender_female" value="Female" required> <label for="gender_female">Female</label>
                </div>
            </div>
            <div class="form-group">
                <label for="nationality">Nationality</label>
                <input type="text" name="nationality" id="nationality" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="marital_status">Marital Status</label>
                <select name="marital_status" id="marital_status" class="form-control" required>
                    <option value="Single">Single</option>
                    <option value="Married">Married</option>
                    <option value="Divorced">Divorced</option>
                    <option value="Widowed">Widowed</option>
                </select>
            </div>
            <div class="form-group">
                <label for="region_id">Region</label>
                <select name="region_id" id="region_id" class="form-control" required>
                    <!-- Populate with regions from the database -->
                    <?php
                    $stmt = $pdo->query("SELECT * FROM Region");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value=\"{$row['region_id']}\">{$row['region_name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="zone_id">Zone</label>
                <select name="zone_id" id="zone_id" class="form-control" required>
                    <!-- Options will be loaded based on region selection -->
                </select>
            </div>
            <div class="form-group">
                <label for="sub_city_id">Sub-city</label>
                <select name="sub_city_id" id="sub_city_id" class="form-control" required>
                    <!-- Options will be loaded based on zone selection -->
                </select>
            </div>
            <div class="form-group">
                <label for="woreda_id">Woreda</label>
                <select name="woreda_id" id="woreda_id" class="form-control" required>
                    <!-- Options will be loaded based on sub-city selection -->
                </select>
            </div>
            <div class="form-group">
                <label for="kebele_id">Kebele</label>
                <select name="kebele_id" id="kebele_id" class="form-control" required>
                    <!-- Options will be loaded based on woreda selection -->
                </select>
            </div>
            <div class="form-group">
                <label for="email_address">Email Address</label>
                <input type="email" name="email_address" id="email_address" class="form-control">
            </div>
            <div class="form-group">
                <label for="mobile_numbers">Mobile Numbers</label>
                <div id="mobile_numbers_container">
                    <input type="text" name="mobile_numbers[]" class="form-control" placeholder="Mobile Number">
                </div>
                <button type="button" id="add_mobile_number" class="btn btn-secondary mt-2">+ Add Mobile Number</button>
            </div>
            <div class="form-group">
                <label for="mobile_number_sms">Mobile Number for SMS</label>
                <input type="text" name="mobile_number_sms" id="mobile_number_sms" class="form-control">
            </div>
            <div class="form-group">
                <label for="education_level">Education Level</label>
                <select name="education_level" id="education_level" class="form-control" required>
                    <option value="None">None</option>
                    <option value="Primary">Primary</option>
                    <option value="Secondary">Secondary</option>
                    <option value="Diploma">Diploma</option>
                    <option value="Bachelor">Bachelor</option>
                    <option value="Master">Master</option>
                    <option value="Doctorate">Doctorate</option>
                </select>
            </div>
            <div class="form-group">
                <label for="profession">Profession</label>
                <input type="text" name="profession" id="profession" class="form-control">
            </div>
            <div class="form-group">
                <label for="occupation">Occupation</label>
                <input type="text" name="occupation" id="occupation" class="form-control">
            </div>
            <div class="form-group">
                <label for="monthly_income">Monthly Income</label>
                <input type="number" name="monthly_income" id="monthly_income" class="form-control">
            </div>
            <div class="form-group">
                <label for="family_size_male">Family Size (Male)</label>
                <input type="number" name="family_size_male" id="family_size_male" class="form-control">
            </div>
            <div class="form-group">
                <label for="family_size_female">Family Size (Female)</label>
                <input type="number" name="family_size_female" id="family_size_female" class="form-control">
            </div>
            <div class="form-group">
                <label for="disability">Disability</label>
                <div>
                    <input type="radio" name="disability" id="disability_no" value="0" required> <label for="disability_no">No</label>
                    <input type="radio" name="disability" id="disability_yes" value="1" required> <label for="disability_yes">Yes</label>
                </div>
            </div>
            <div class="form-group">
                <label for="regular_saving_payment_mode">Regular Saving Payment Mode</label>
                <div>
                    <input type="radio" name="regular_saving_payment_mode" id="fixed_instalment" value="Fixed Instalment" required> <label for="fixed_instalment">Fixed Instalment</label>
                    <input type="radio" name="regular_saving_payment_mode" id="monthly_income_10" value="10% of Monthly Income" required> <label for="monthly_income_10">10% of Monthly Income</label>
                </div>
            </div>
            <div class="form-group">
                <label for="saving_account_type">Saving Account Type</label>
                <select name="saving_account_type" id="saving_account_type" class="form-control" required>
                    <option value="Regular">Regular</option>
                    <option value="Housing">Housing</option>
                    <option value="Automobil">Automobil</option>
                    <option value="Transport">Transport</option>
                    <option value="Trade">Trade</option>
                </select>
            </div>
            <div class="form-group">
                <label for="service_type">Service Type</label>
                <div>
                    <input type="radio" name="service_type" id="interest_barring" value="interest barring" required> <label for="interest_barring">Interest Barring</label>
                    <input type="radio" name="service_type" id="non_interest_barring" value="non-interest barring" required> <label for="non_interest_barring">Non-Interest Barring</label>
                </div>
            </div>
            <div class="form-group">
                <label for="source_of_info">Source of Information</label>
                <input type="text" name="source_of_info" id="source_of_info" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Add Member</button>
        </form>
    </div>
</body>
</html>
