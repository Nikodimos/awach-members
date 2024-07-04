<?php
require_once './config/session_check.php';
require './config/database.php';

$branch_id = $_SESSION['branch_id'];
$today = date('Y-m-d');
$min_date_of_birth = date('Y-m-d', strtotime('-18 years'));

$opening_date = $member_id = $title_id = $member_name = $member_father_name = $member_grandfather_name = '';
$member_mother_name = $member_mother_father_name = $member_mother_grandfather_name = '';
$date_of_birth = $gender = $nationality = $marital_status = '';
$region_id = $zone_id = $city_id = $sub_city_id = $woreda_id = $kebele_id = $house_number = '';
$email_address = $mobile_number_sms = $education_level = $profession = $occupation = '';
$monthly_income = $family_size_male = $family_size_female = '';
$disability = 0;
$regular_saving_payment_mode = 'Fixed Instalment';
$saving_account_type = '';
$service_type = 'Interest Barring';
$source_of_info = '';

$mobile_numbers = [];

$errors = [];
$warnings = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    $opening_date = $_POST['opening_date'] ?? '';
    $member_id = $_POST['member_id'] ?? '';
    $title_id = $_POST['title_id'] ?? '';
    $member_name = $_POST['member_name'] ?? '';
    $member_father_name = $_POST['member_father_name'] ?? '';
    $member_grandfather_name = $_POST['member_grandfather_name'] ?? '';
    $member_mother_name = $_POST['member_mother_name'] ?? '';
    $member_mother_father_name = $_POST['member_mother_father_name'] ?? '';
    $member_mother_grandfather_name = $_POST['member_mother_grandfather_name'] ?? '';
    $date_of_birth = $_POST['date_of_birth'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $nationality = $_POST['nationality'] ?? 'Ethiopian';
    $marital_status = $_POST['marital_status'] ?? '';
    $region_id = $_POST['region_id'] ?? '';
    $zone_id = $_POST['zone_id'] ?? '';
    $city_id = $_POST['city_id'] ?? '';
    $sub_city_id = $_POST['sub_city_id'] ?? '';
    $woreda_id = $_POST['woreda_id'] ?? '';
    $kebele_id = $_POST['kebele_id'] ?? '';
    $house_number = $_POST['house_number'] ?? '';
    $email_address = $_POST['email_address'] ?? '';
    $mobile_numbers = $_POST['mobile_numbers'] ?? [];
    $mobile_number_sms = $_POST['mobile_number_sms'] ?? ($mobile_numbers[0] ?? '');
    $education_level = $_POST['education_level'] ?? '';
    $profession = $_POST['profession'] ?? '';
    $occupation = $_POST['occupation'] ?? '';
    $monthly_income = $_POST['monthly_income'] ?? '';
    $family_size_male = $_POST['family_size_male'] ?? '';
    $family_size_female = $_POST['family_size_female'] ?? '';
    $disability = isset($_POST['disability']) ? 1 : 0;
    $regular_saving_payment_mode = $_POST['regular_saving_payment_mode'] ?? 'Fixed Instalment';
    $saving_account_type = $_POST['saving_account_type'] ?? '';
    $service_type = $_POST['service_type'] ?? 'Interest Barring';
    $source_of_info = $_POST['source_of_info'] ?? '';
    $user_id = $_SESSION['user_id'];

    // Validation
    if (empty($opening_date)) {
        $errors[] = "Opening date is required.";
    } elseif ($opening_date > $today) {
        $errors[] = "Opening date cannot be a future date.";
    }

    if (empty($member_id)) {
        $errors[] = "Member ID is required.";
    } elseif (strlen($member_id) != 10 || $member_id[0] != '2') {
        $errors[] = "Member ID must be 10 characters long and start with '2'.";
    } elseif (strlen($member_id) < 10) {
        $warnings[] = "Member ID is less than 10 characters. Do you want to continue?";
    }

    if (empty($member_name)) {
        $errors[] = "Member name is required.";
    }

    if (empty($member_father_name)) {
        $errors[] = "Father's name is required.";
    }

    if (empty($member_grandfather_name)) {
        $errors[] = "Grandfather's name is required.";
    }

    if (empty($member_mother_name)) {
        $errors[] = "Mother's name is required.";
    }

    if (empty($member_mother_father_name)) {
        $errors[] = "Mother's father's name is required.";
    }

    if (empty($date_of_birth)) {
        $errors[] = "Date of birth is required.";
    } elseif ($date_of_birth > $min_date_of_birth) {
        $errors[] = "Member must be at least 18 years old.";
    }

    if (empty($gender)) {
        $errors[] = "Gender is required.";
    }

    if (empty($marital_status)) {
        $errors[] = "Marital status is required.";
    }

    if (empty($region_id)) {
        $errors[] = "Region is required.";
    }

    if (empty($zone_id)) {
        $errors[] = "Zone is required.";
    }

    if (empty($city_id)) {
        $errors[] = "City is required.";
    }

    if (empty($sub_city_id)) {
        $errors[] = "Sub-City is required.";
    }

    if (empty($woreda_id)) {
        $errors[] = "Woreda is required.";
    }

    if (empty($kebele_id)) {
        $errors[] = "Kebele is required.";
    }

    if (empty($house_number)) {
        $errors[] = "House number is required.";
    }

    if (empty($mobile_numbers[0])) {
        $errors[] = "At least one mobile number is required.";
    }

    // Title ID logic based on gender and marital status
    if (empty($title_id)) {
        if ($gender == 'Male') {
            $title_id = 1;
        } elseif ($gender == 'Female' && $marital_status == 'Single') {
            $title_id = 2;
        } else {
            $title_id = 3;
        }
    }

    // Add Member logic
    if ($action == 'add_member' && empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO Members (branch_id, opening_date, member_id, title_id, member_name, member_father_name, member_grandfather_name, member_mother_name, member_mother_father_name, member_mother_grandfather_name, date_of_birth, gender, nationality, marital_status, region_id, zone_id, city_id, sub_city_id, woreda_id, kebele_id, house_number, email_address, mobile_numbers, mobile_number_sms, education_level, profession, occupation, monthly_income, family_size_male, family_size_female, disability, regular_saving_payment_mode, saving_account_type, service_type, source_of_info, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        if ($stmt->execute([$branch_id, $opening_date, $member_id, $title_id, $member_name, $member_father_name, $member_grandfather_name, $member_mother_name, $member_mother_father_name, $member_mother_grandfather_name, $date_of_birth, $gender, $nationality, $marital_status, $region_id, $zone_id, $city_id, $sub_city_id, $woreda_id, $kebele_id, $house_number, $email_address, implode(',', $mobile_numbers), $mobile_number_sms, $education_level, $profession, $occupation, $monthly_income, $family_size_male, $family_size_female, $disability, $regular_saving_payment_mode, $saving_account_type, $service_type, $source_of_info, $user_id])) {
            // Record in audit trail
            $details = json_encode(['member_id' => $member_id, 'member_name' => $member_name]);
            $audit_stmt = $pdo->prepare("INSERT INTO audit_trail (user_id, action, details) VALUES (?, 'insert', ?)");
            $audit_stmt->execute([$user_id, $details]);

            header("Location: members.php");
            exit;
        } else {
            $errors[] = "Failed to add member.";
        }
    }
}

// Fetch dropdown options
$branches_stmt = $pdo->query('SELECT branch_id, branch_name FROM branches');
$branches = $branches_stmt->fetchAll(PDO::FETCH_ASSOC);

$titles_stmt = $pdo->query('SELECT title_id, title_name FROM Title');
$titles = $titles_stmt->fetchAll(PDO::FETCH_ASSOC);

$regions_stmt = $pdo->query('SELECT region_id, region_name FROM Region');
$regions = $regions_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Member</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Add Member</h2>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($warnings)): ?>
        <div class="alert alert-warning">
            <?php foreach ($warnings as $warning): ?>
                <p><?php echo $warning; ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post" action="member_add.php">
        <input type="hidden" name="action" value="validate">

        <div class="form-group row">
            <div class="col">
                <label for="branch_id">Branch</label>
                <select name="branch_id" id="branch_id" class="form-control" disabled>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?php echo $branch['branch_id']; ?>" <?php echo $branch['branch_id'] == $branch_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($branch['branch_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label for="opening_date">Opening Date <span class="text-danger">*</span></label>
                <input type="date" name="opening_date" id="opening_date" class="form-control" max="<?php echo $today; ?>" value="<?php echo htmlspecialchars($opening_date, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="member_id">Member ID <span class="text-danger">*</span></label>
                <input type="text" name="member_id" id="member_id" class="form-control" value="<?php echo htmlspecialchars($member_id, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="title_id">Title <span class="text-danger">*</span></label>
                <select name="title_id" id="title_id" class="form-control">
                    <option value="">Select Title</option>
                    <?php foreach ($titles as $title): ?>
                        <option value="<?php echo $title['title_id']; ?>" <?php echo $title['title_id'] == $title_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($title['title_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="member_name">Member Name <span class="text-danger">*</span></label>
                <input type="text" name="member_name" id="member_name" class="form-control" value="<?php echo htmlspecialchars($member_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="member_father_name">Father's Name <span class="text-danger">*</span></label>
                <input type="text" name="member_father_name" id="member_father_name" class="form-control" value="<?php echo htmlspecialchars($member_father_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="member_grandfather_name">Grandfather's Name <span class="text-danger">*</span></label>
                <input type="text" name="member_grandfather_name" id="member_grandfather_name" class="form-control" value="<?php echo htmlspecialchars($member_grandfather_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="member_mother_name">Mother's Name <span class="text-danger">*</span></label>
                <input type="text" name="member_mother_name" id="member_mother_name" class="form-control" value="<?php echo htmlspecialchars($member_mother_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="member_mother_father_name">Mother's Father's Name <span class="text-danger">*</span></label>
                <input type="text" name="member_mother_father_name" id="member_mother_father_name" class="form-control" value="<?php echo htmlspecialchars($member_mother_father_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="member_mother_grandfather_name">Mother's Grandfather's Name</label>
                <input type="text" name="member_mother_grandfather_name" id="member_mother_grandfather_name" class="form-control" value="<?php echo htmlspecialchars($member_mother_grandfather_name, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                <input type="date" name="date_of_birth" id="date_of_birth" class="form-control" max="<?php echo $min_date_of_birth; ?>" value="<?php echo htmlspecialchars($date_of_birth, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="gender">Gender <span class="text-danger">*</span></label>
                <div>
                    <input type="radio" name="gender" value="Male" id="gender_male" <?php echo $gender == 'Male' ? 'checked' : ''; ?>>
                    <label for="gender_male">Male</label>
                    <input type="radio" name="gender" value="Female" id="gender_female" <?php echo $gender == 'Female' ? 'checked' : ''; ?>>
                    <label for="gender_female">Female</label>
                </div>
            </div>
            <div class="col">
                <label for="nationality">Nationality <span class="text-danger">*</span></label>
                <input type="text" name="nationality" id="nationality" class="form-control" value="<?php echo htmlspecialchars($nationality, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="marital_status">Marital Status <span class="text-danger">*</span></label>
                <div>
                    <input type="radio" name="marital_status" value="Single" id="marital_status_single" <?php echo $marital_status == 'Single' ? 'checked' : ''; ?>>
                    <label for="marital_status_single">Single</label>
                    <input type="radio" name="marital_status" value="Married" id="marital_status_married" <?php echo $marital_status == 'Married' ? 'checked' : ''; ?>>
                    <label for="marital_status_married">Married</label>
                    <input type="radio" name="marital_status" value="Divorced" id="marital_status_divorced" <?php echo $marital_status == 'Divorced' ? 'checked' : ''; ?>>
                    <label for="marital_status_divorced">Divorced</label>
                    <input type="radio" name="marital_status" value="Widowed" id="marital_status_widowed" <?php echo $marital_status == 'Widowed' ? 'checked' : ''; ?>>
                    <label for="marital_status_widowed">Widowed</label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="region_id">Region <span class="text-danger">*</span></label>
                <select name="region_id" id="region_id" class="form-control">
                    <option value="">Select Region</option>
                    <?php foreach ($regions as $region): ?>
                        <option value="<?php echo $region['region_id']; ?>" <?php echo $region['region_id'] == $region_id ? 'selected' : ''; ?>><?php echo htmlspecialchars($region['region_name'], ENT_QUOTES, 'UTF-8'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col">
                <label for="zone_id">Zone <span class="text-danger">*</span></label>
                <select name="zone_id" id="zone_id" class="form-control">
                    <option value="">Select Zone</option>
                </select>
            </div>
            <div class="col">
                <label for="city_id">City <span class="text-danger">*</span></label>
                <select name="city_id" id="city_id" class="form-control">
                    <option value="">Select City</option>
                </select>
            </div>
            <div class="col">
                <label for="sub_city_id">Sub-City <span class="text-danger">*</span></label>
                <select name="sub_city_id" id="sub_city_id" class="form-control">
                    <option value="">Select Sub-City</option>
                </select>
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="woreda_id">Woreda <span class="text-danger">*</span></label>
                <select name="woreda_id" id="woreda_id" class="form-control">
                    <option value="">Select Woreda</option>
                </select>
            </div>
            <div class="col">
                <label for="kebele_id">Kebele <span class="text-danger">*</span></label>
                <select name="kebele_id" id="kebele_id" class="form-control">
                    <option value="">Select Kebele</option>
                </select>
            </div>
            <div class="col">
                <label for="house_number">House Number <span class="text-danger">*</span></label>
                <input type="text" name="house_number" id="house_number" class="form-control" value="<?php echo htmlspecialchars($house_number, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="email_address">Email Address</label>
                <input type="email" name="email_address" id="email_address" class="form-control" value="<?php echo htmlspecialchars($email_address, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="mobile_numbers">Mobile Numbers <span class="text-danger">*</span></label>
                <div id="mobile_numbers_wrapper">
                    <?php if (!empty($mobile_numbers)): ?>
                        <?php foreach ($mobile_numbers as $number): ?>
                            <input type="text" name="mobile_numbers[]" class="form-control mb-2" value="<?php echo htmlspecialchars($number, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php endforeach; ?>
                    <?php else: ?>
                        <input type="text" name="mobile_numbers[]" class="form-control mb-2">
                    <?php endif; ?>
                </div>
                <button type="button" id="add_mobile_number" class="btn btn-secondary">+ Add Mobile Number</button>
            </div>
            <div class="col">
                <label for="mobile_number_sms">Mobile Number for SMS <span class="text-danger">*</span></label>
                <input type="text" name="mobile_number_sms" id="mobile_number_sms" class="form-control" value="<?php echo htmlspecialchars($mobile_number_sms, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="education_level">Education Level <span class="text-danger">*</span></label>
                <select name="education_level" id="education_level" class="form-control">
                    <option value="None" <?php echo $education_level == 'None' ? 'selected' : ''; ?>>None</option>
                    <option value="Primary" <?php echo $education_level == 'Primary' ? 'selected' : ''; ?>>Primary</option>
                    <option value="Secondary" <?php echo $education_level == 'Secondary' ? 'selected' : ''; ?>>Secondary</option>
                    <option value="Diploma" <?php echo $education_level == 'Diploma' ? 'selected' : ''; ?>>Diploma</option>
                    <option value="Bachelor" <?php echo $education_level == 'Bachelor' ? 'selected' : ''; ?>>Bachelor</option>
                    <option value="Master" <?php echo $education_level == 'Master' ? 'selected' : ''; ?>>Master</option>
                    <option value="Doctorate" <?php echo $education_level == 'Doctorate' ? 'selected' : ''; ?>>Doctorate</option>
                </select>
            </div>
            <div class="col">
                <label for="profession">Profession</label>
                <input type="text" name="profession" id="profession" class="form-control" value="<?php echo htmlspecialchars($profession, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="occupation">Occupation</label>
                <input type="text" name="occupation" id="occupation" class="form-control" value="<?php echo htmlspecialchars($occupation, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="monthly_income">Monthly Income</label>
                <input type="number" name="monthly_income" id="monthly_income" class="form-control" value="<?php echo htmlspecialchars($monthly_income, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="family_size_male">Family Size (Male)</label>
                <input type="number" name="family_size_male" id="family_size_male" class="form-control" value="<?php echo htmlspecialchars($family_size_male, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col">
                <label for="family_size_female">Family Size (Female)</label>
                <input type="number" name="family_size_female" id="family_size_female" class="form-control" value="<?php echo htmlspecialchars($family_size_female, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="disability">Disability <span class="text-danger">*</span></label>
                <div>
                    <input type="radio" name="disability" value="1" id="disability_yes" <?php echo $disability ? 'checked' : ''; ?>>
                    <label for="disability_yes">Yes</label>
                    <input type="radio" name="disability" value="0" id="disability_no" <?php echo !$disability ? 'checked' : ''; ?>>
                    <label for="disability_no">No</label>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="col">
                <label for="regular_saving_payment_mode">Regular Saving Payment Mode <span class="text-danger">*</span></label>
                <div>
                    <input type="radio" name="regular_saving_payment_mode" value="Fixed Instalment" id="payment_mode_fixed" <?php echo $regular_saving_payment_mode == 'Fixed Instalment' ? 'checked' : ''; ?>>
                    <label for="payment_mode_fixed">Fixed Instalment</label>
                    <input type="radio" name="regular_saving_payment_mode" value="10% of Monthly Income" id="payment_mode_percentage" <?php echo $regular_saving_payment_mode == '10% of Monthly Income' ? 'checked' : ''; ?>>
                    <label for="payment_mode_percentage">10% of Monthly Income</label>
                </div>
            </div>
            <div class="col">
                <label for="saving_account_type">Saving Account Type <span class="text-danger">*</span></label>
                <select name="saving_account_type" id="saving_account_type" class="form-control">
                    <option value="Regular" <?php echo $saving_account_type == 'Regular' ? 'selected' : ''; ?>>Regular</option>
                    <option value="Housing" <?php echo $saving_account_type == 'Housing' ? 'selected' : ''; ?>>Housing</option>
                    <option value="Automobile" <?php echo $saving_account_type == 'Automobile' ? 'selected' : ''; ?>>Automobile</option>
                    <option value="Transport" <?php echo $saving_account_type == 'Transport' ? 'selected' : ''; ?>>Transport</option>
                    <option value="Trade" <?php echo $saving_account_type == 'Trade' ? 'selected' : ''; ?>>Trade</option>
                </select>
            </div>
            <div class="col">
                <label for="service_type">Service Type <span class="text-danger">*</span></label>
                <div>
                    <input type="radio" name="service_type" value="Interest Barring" id="service_type_interest" <?php echo $service_type == 'Interest Barring' ? 'checked' : ''; ?>>
                    <label for="service_type_interest">Interest Barring</label>
                    <input type="radio" name="service_type" value="Non-Interest Barring" id="service_type_non_interest" <?php echo $service_type == 'Non-Interest Barring' ? 'checked' : ''; ?>>
                    <label for="service_type_non_interest">Non-Interest Barring</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label for="source_of_info">Source of Information</label>
            <input type="text" name="source_of_info" id="source_of_info" class="form-control" value="<?php echo htmlspecialchars($source_of_info, ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <button type="button" id="validate" class="btn btn-info">Validate</button>
        <button type="submit" name="action" value="add_member" class="btn btn-primary">Add Member</button>
    </form>
</div>

<script>
$(document).ready(function() {
    // Dynamic address subdivisions
    $('#region_id').change(function() {
        var region_id = $(this).val();
        $.ajax({
            url: 'get_zones.php',
            method: 'POST',
            data: { region_id: region_id },
            success: function(response) {
                $('#zone_id').html(response);
                $('#city_id').html('<option value="">Select City</option>');
                $('#sub_city_id').html('<option value="">Select Sub-City</option>');
                $('#woreda_id').html('<option value="">Select Woreda</option>');
                $('#kebele_id').html('<option value="">Select Kebele</option>');
            }
        });
    });

    $('#zone_id').change(function() {
        var zone_id = $(this).val();
        $.ajax({
            url: 'get_cities.php',
            method: 'POST',
            data: { zone_id: zone_id },
            success: function(response) {
                $('#city_id').html(response);
                $('#sub_city_id').html('<option value="">Select Sub-City</option>');
                $('#woreda_id').html('<option value="">Select Woreda</option>');
                $('#kebele_id').html('<option value="">Select Kebele</option>');
            }
        });
    });

    $('#city_id').change(function() {
        var city_id = $(this).val();
        $.ajax({
            url: 'get_sub_cities.php',
            method: 'POST',
            data: { city_id: city_id },
            success: function(response) {
                $('#sub_city_id').html(response);
                $('#woreda_id').html('<option value="">Select Woreda</option>');
                $('#kebele_id').html('<option value="">Select Kebele</option>');
            }
        });
    });

    $('#sub_city_id').change(function() {
        var sub_city_id = $(this).val();
        $.ajax({
            url: 'get_woredas.php',
            method: 'POST',
            data: { sub_city_id: sub_city_id },
            success: function(response) {
                $('#woreda_id').html(response);
                $('#kebele_id').html('<option value="">Select Kebele</option>');
            }
        });
    });

    $('#woreda_id').change(function() {
        var woreda_id = $(this).val();
        $.ajax({
            url: 'get_kebeles.php',
            method: 'POST',
            data: { woreda_id: woreda_id },
            success: function(response) {
                $('#kebele_id').html(response);
            }
        });
    });

    // Add more mobile numbers
    $('#add_mobile_number').click(function() {
        $('#mobile_numbers_wrapper').append('<input type="text" name="mobile_numbers[]" class="form-control mb-2">');
    });

    // Validate button
    $('#validate').click(function() {
        $('input[name="action"]').val('validate');
        $('form').submit();
    });
});
</script>
</body>
</html>
