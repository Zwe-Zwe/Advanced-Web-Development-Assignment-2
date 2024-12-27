<?php
session_name('Zwe_Het_Zaw');
session_start();



include_once 'connection.php';
$dbname = "PlantBiodiversity";

// Function to validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate password complexity
function validatePassword($password) {
    return preg_match('/^(?=.*\d)(?=.*[\W_]).{8,}$/', $password);
}

// Function to check if the email already exists in the database
function isEmailTaken($email, $conn) {
    $stmt = $conn->prepare("SELECT email FROM user_table WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();
    return $count > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $first_name = isset($_POST["first_name"]) ? trim($_POST["first_name"]) : '';
    $last_name = isset($_POST["last_name"]) ? trim($_POST["last_name"]) : '';
    $dob = isset($_POST["dob"]) ? $_POST["dob"] : '';
    $gender = isset($_POST["gender"]) ? $_POST["gender"] : '';
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $home_town = isset($_POST["home_town"]) ? trim($_POST["home_town"]) : '';
    $phone_number = isset($_POST["phone_number"]) ? trim($_POST["phone_number"]) : '';
    $student_id = isset($_POST["student_id"]) ? trim($_POST["student_id"]) : '';
    $password = isset($_POST["password"]) ? $_POST["password"] : '';
    $confirm_password = isset($_POST["confirm_password"]) ? $_POST["confirm_password"] : '';

    // Array to hold validation errors
    $errors = [];

    // Validate fields and check required fields
    if (empty($first_name)) { $errors['first_name'] = "First name cannot be empty."; }
    if (empty($last_name)) { $errors['last_name'] = "Last name cannot be empty."; }
    if (empty($dob)) { $errors['dob'] = "Date of Birth cannot be empty."; }
    if (empty($email)) { $errors['email'] = "Email cannot be empty."; }
    if (empty($home_town)) { $errors['home_town'] = "Home town cannot be empty."; }
    if (empty($phone_number)) { $errors['phone_number'] = "Phone number cannot be empty."; }
    if (empty($student_id)) { $errors['student_id'] = "Student ID cannot be empty."; }
    if (empty($password)) { $errors['password'] = "Password cannot be empty."; }
    if (empty($confirm_password)) { $errors['confirm_password'] = "Confirm Password cannot be empty."; }

    // Additional validation for specific fields
    if (!empty($first_name) && !preg_match("/^[a-zA-Z\s]+$/", $first_name)) { $errors['first_name'] = "First name must contain only alphabets and spaces."; }
    if (!empty($last_name) && !preg_match("/^[a-zA-Z\s]+$/", $last_name)) { $errors['last_name'] = "Last name must contain only alphabets and spaces."; }
    if (!empty($email) && !validateEmail($email)) { $errors['email'] = "Invalid email format."; }
    if (!empty($email) && isEmailTaken($email, $conn)) { $errors['email'] = "This email is already registered."; }
    if (!empty($phone_number) && !preg_match("/^[0-9]{7,15}$/", $phone_number)) { $errors['phone_number'] = "Phone number must be between 7 and 15 digits."; }
    if (!empty($password) && !validatePassword($password)) { $errors['password'] = "Password must be at least 8 characters long and include 1 number and 1 symbol."; }
    if (!empty($password) && !empty($confirm_password) && $password !== $confirm_password) { $errors['confirm_password'] = "Password and Confirm Password do not match."; }

    // If validation passes, proceed to insert data
    if (empty($errors)) {
        // Assign profile image based on gender
        $profile_img = ($gender === 'Male') ? 'boys.jpg' : (($gender === 'Female') ? 'girl.png' : 'default.png');

        // Hash the password for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert into user_table
        $stmt = $conn->prepare("INSERT INTO user_table (email, first_name, last_name, dob, gender, contact_number, hometown, profile_image, student_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssss", $email, $first_name, $last_name, $dob, $gender, $phone_number, $home_town, $profile_img, $student_id);
        $stmt->execute();
        $stmt->close();

        // Define user type and insert into account_table
        $type = 'user';
        $stmt = $conn->prepare("INSERT INTO account_table (email, password, type) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashed_password, $type);
        $stmt->execute();
        $stmt->close();

        // Redirect on success
        $_SESSION['registration_success'] = true;
        header("Location: registration.php");
        exit();
    } else {
        // Redirect with error messages and form data if validation fails
        $_SESSION['errors'] = $errors;
        $_SESSION['form_data'] = $_POST;
        header("Location: registration.php");
        exit();
    }
}

$conn->close();
?>
