<?php
session_name('Zwe_Het_Zaw');
session_start();

// Set timeout duration to 30 minutes
$timeout_duration = 1800; // 30 minutes in seconds

// Check if 'last_activity' is set in session
if (isset($_SESSION['last_activity'])) {
    // If the last activity time is more than 30 minutes ago, log out the user
    if (time() - $_SESSION['last_activity'] > $timeout_duration) {
        // Clear the session and redirect to login
        session_unset();
        session_destroy();
        header("Location: login.php?message=Your session has expired due to inactivity.");
        exit();  // Make sure the script stops executing after the redirect
    }
}

// Update the last activity timestamp to the current time
$_SESSION['last_activity'] = time();

// Include database connection
include_once 'connection.php';

// Function to fetch user data from the database
function getUserData($email, $conn) {
    $sql = "SELECT * FROM user_table WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_assoc(); // Return the user data as an associative array
}

// Function to fetch password from the account_table
function getUserPassword($email, $conn) {
    $sql = "SELECT password FROM account_table WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $row = $result->fetch_assoc();
    return $row['password'] ?? ''; // Return the password, or an empty string if not found
}

// Function to check if an email exists in the account_table
function emailExists($email, $conn) {
    $sql = "SELECT email FROM account_table WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0; // Return true if email exists, false otherwise
}

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    header("Location: login.php");
    exit();
}

// Get the current user's email from session
$current_email = $_SESSION['email'];

// Fetch user data from the user_table
$user_data = getUserData($current_email, $conn);
if ($user_data === null) {
    echo "User data not found.";
    exit();
}

// Fetch user password from the account_table
$password = getUserPassword($current_email, $conn);

// Extract current user information
$first_name = $user_data['first_name'];
$last_name = $user_data['last_name'];
$dob = $user_data['dob'];
$home_town = $user_data['hometown'];
$phone_number = $user_data['contact_number'];
$student_id = $user_data['student_id'];
$profile_image = $user_data['profile_image'] ?? ''; // Extract profile image filename

$profile_image_path = !empty($profile_image) ? "img/profile_images/" . htmlspecialchars($profile_image) : "img/profile_images/default.png"; // Fallback to default image

$errors = [
    'first_name' => '',
    'last_name' => '',
    'dob' => '',
    'home_town' => '',
    'phone_number' => '',
    'student_id' => '',
    'email' => '',
    'password' => '',
    'confirm_password' => '',
];

// Update profile form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $first_name = isset($_POST["first_name"]) ? trim($_POST["first_name"]) : '';
    $last_name = isset($_POST["last_name"]) ? trim($_POST["last_name"]) : '';
    $dob = isset($_POST["dob"]) ? $_POST["dob"] : '';
    $home_town = isset($_POST["home_town"]) ? trim($_POST["home_town"]) : '';
    $phone_number = isset($_POST["phone_number"]) ? trim($_POST["phone_number"]) : '';
    $student_id = isset($_POST["student_id"]) ? trim($_POST["student_id"]) : '';
    $new_email = isset($_POST["email"]) ? trim($_POST["email"]) : '';

    // Validation
    if (empty($first_name)) {
        $errors['first_name'] = "First name is required.";
    }
    if (empty($last_name)) {
        $errors['last_name'] = "Last name is required.";
    }
    if (empty($dob)) {
        $errors['dob'] = "Date of birth is required.";
    }
    if (empty($phone_number)) {
        $errors['phone_number'] = "Phone number is required.";
    }
    if (empty($student_id)) {
        $errors['student_id'] = "Student ID is required.";
    }
    
    if ($new_email !== $current_email) {
        if (emailExists($new_email, $conn)) {
            $errors['email'] = "The email address is already in use. Please choose another one.";
        }
    }

    // Handle password update if provided
    if (isset($_POST["password"]) && !empty($_POST["password"])) {
        $new_password = trim($_POST["password"]);
        $confirm_password = trim($_POST["confirm_password"] ?? '');

        if ($new_password !== $confirm_password) {
            $errors['password'] = "Passwords do not match.";
        }
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the new password

        $sql = "UPDATE account_table SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ss', $hashed_password, $current_email);
        $stmt->execute();
    }

    // If there are validation errors, do not process further
    if (array_filter($errors)) {
        // Skip processing and display errors
    } else {
        // Handle profile image upload
        $new_profile_image = '';
        $max_file_size = 5 * 1024 * 1024; // 5MB in bytes

        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $upload_file = $_FILES['profile_image'];
            $upload_dir = 'img/profile_images/';
            $file_name = basename($upload_file['name']);
            $target_file = $upload_dir . $file_name;

            // Check file type (e.g., jpeg, png)
            $valid_extensions = ['jpg', 'jpeg', 'png'];
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            if (!in_array($file_extension, $valid_extensions)) {
                echo "Invalid file type. Only JPG, JPEG, and PNG are allowed.";
                exit();
            }

            // Check file size
            if ($upload_file['size'] > $max_file_size) {
                echo "File is too large. Maximum allowed size is 5MB.";
                exit();
            }

            // Move uploaded file
            if (move_uploaded_file($upload_file['tmp_name'], $target_file)) {
                $new_profile_image = $file_name;
                $_SESSION['profile_image'] = $new_profile_image; // Optionally store in session
            } else {
                echo "Error uploading file.";
                exit();
            }
        } else {
            $new_profile_image = $profile_image; // Keep the old profile image if no new image is uploaded
        }

        // Update user data in user_table
        $sql = "UPDATE user_table SET first_name = ?, last_name = ?, dob = ?, hometown = ?, contact_number = ?, student_id = ?, profile_image = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssss', $first_name, $last_name, $dob, $home_town, $phone_number, $student_id, $new_profile_image, $current_email);

        if ($stmt->execute()) {
            // Handle email change and other updates as before
            if ($new_email !== $current_email) {
                $sql = "UPDATE user_table SET email = ? WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $new_email, $current_email);
                $stmt->execute();

                $sql = "UPDATE account_table SET email = ? WHERE email = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('ss', $new_email, $current_email);
                $stmt->execute();

                $_SESSION['email'] = $new_email; // Update session email
            }

            $_SESSION['update_success'] = true;
            header("Location: main_menu.php");
            exit();
        } else {
            echo "Error updating profile: " . $conn->error;
        }

    }
}

// Handle resume upload (PDF, max 7MB)
$new_resume = '';
$max_resume_size = 7 * 1024 * 1024; // 7MB in bytes

if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
    $upload_file = $_FILES['resume'];
    $upload_dir = 'resume/';  // Folder to store resumes
    $file_name = basename($upload_file['name']);
    $target_file = $upload_dir . $file_name;

    // Check if the file is a PDF
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if ($file_extension !== 'pdf') {
        $errors['resume'] = "Only PDF files are allowed.";
    }

    // Check file size
    if ($upload_file['size'] > $max_resume_size) {
        $errors['resume'] = "File is too large. Maximum allowed size is 7MB.";
    }

    // If no errors, move the file to the target directory
    if (empty($errors['resume']) && move_uploaded_file($upload_file['tmp_name'], $target_file)) {
        $new_resume = $file_name;
    } else {
        // If errors exist, we display them in the form
        if (!empty($errors['resume'])) {
            echo $errors['resume'];  // Display error message
            exit();
        }
    }
} else {
    $new_resume = ''; // If no resume is uploaded, just leave it empty
}

// Proceed with profile update if no other errors exist
if (array_filter($errors) === []) {
    // Handle other profile updates (e.g., name, email, etc.) as before
}

?>


<!-- HTML section of the update profile page -->
<?php include_once "head.php"; ?>
<body id="reg-body">
    <?php include_once "header.php"; ?>
    <div class="container d-flex justify-content-center" id="update-container">
        <form id="update-form" method="POST" action="update_profile.php" enctype="multipart/form-data">

            <!-- Profile Image Field -->
            <div class="row mb-3">
                <div class="col text-center">
                    <img src="<?= $profile_image_path; ?>" alt="Profile Image" class="img-thumbnail" width="150"><br>
                    <label for="profile_image">Change Profile Image:</label>
                    <input type="file" name="profile_image" id="profile_image" class="custom-input <?= !empty($errors['profile_image']) ? 'is-invalid' : ''; ?>" accept="image/*">
                    <?php if (!empty($errors['profile_image'])): ?>
                        <div class="invalid-feedback"><?= $errors['profile_image']; ?></div>
                    <?php endif; ?>
                </div>
            </div>


            <!-- Resume Upload Field -->
            <div class="row mb-3 p-3">
                <div class="col">
                    <label for="resume">Upload Resume (PDF, max 7MB):</label>
                    <input type="file" name="resume" id="resume" class="custom-input <?= !empty($errors['resume']) ? 'is-invalid' : ''; ?>" accept="application/pdf">
                    <?php if (!empty($errors['resume'])): ?>
                        <div class="invalid-feedback"><?= $errors['resume']; ?></div>
                    <?php endif; ?>
                </div>
            </div>


            <!-- First Name, Last Name -->
            <div class="row mb-3 p-3">
                <div class="col">
                    <label for="first_name">First Name:</label>
                    <input type="text" name="first_name" id="first_name" class="custom-input <?= !empty($errors['first_name']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($first_name); ?>" placeholder="First Name" required>
                    <?php if (!empty($errors['first_name'])): ?>
                        <div class="invalid-feedback"><?= $errors['first_name']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <label for="last_name">Last Name:</label>
                    <input type="text" name="last_name" id="last_name" class="custom-input <?= !empty($errors['last_name']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($last_name); ?>" placeholder="Last Name" required>
                    <?php if (!empty($errors['last_name'])): ?>
                        <div class="invalid-feedback"><?= $errors['last_name']; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Date of Birth, Home Town -->
            <div class="row mb-3 p-3">
                <div class="col">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" name="dob" id="dob" class="custom-input <?= !empty($errors['dob']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($dob); ?>" required>
                    <?php if (!empty($errors['dob'])): ?>
                        <div class="invalid-feedback"><?= $errors['dob']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <label for="home_town">Home Town:</label>
                    <input type="text" name="home_town" id="home_town" class="custom-input <?= !empty($errors['home_town']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($home_town); ?>" placeholder="Home Town" required>
                    <?php if (!empty($errors['home_town'])): ?>
                        <div class="invalid-feedback"><?= $errors['home_town']; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Phone Number, Student ID -->
            <div class="row mb-3 p-3">
                <div class="col">
                    <label for="phone_number">Phone Number:</label>
                    <input type="text" name="phone_number" id="phone_number" class="custom-input <?= !empty($errors['phone_number']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($phone_number); ?>" placeholder="Phone Number" required>
                    <?php if (!empty($errors['phone_number'])): ?>
                        <div class="invalid-feedback"><?= $errors['phone_number']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <label for="student_id">Student ID:</label>
                    <input type="text" name="student_id" id="student_id" class="custom-input <?= !empty($errors['student_id']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($student_id); ?>" placeholder="Student ID" required>
                    <?php if (!empty($errors['student_id'])): ?>
                        <div class="invalid-feedback"><?= $errors['student_id']; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Email -->
                <div class="row mb-3 p-3">
                    <div class="col">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="custom-input <?= !empty($errors['email']) ? 'is-invalid' : ''; ?>" value="<?= htmlspecialchars($new_email ?? $current_email); ?>" placeholder="Email" required>
                        <?php if (!empty($errors['email'])): ?>
                            <div class="invalid-feedback"><?= $errors['email']; ?></div>
                        <?php endif; ?>
                    </div>
                </div>


            <!-- Password Fields -->
            <div class="row mb-3 p-3">
                <div class="col">
                    <label for="password">New Password:</label>
                    <input type="password" name="password" id="password" class="custom-input <?= !empty($errors['password']) ? 'is-invalid' : ''; ?>" placeholder="New Password">
                    <?php if (!empty($errors['password'])): ?>
                        <div class="invalid-feedback"><?= $errors['password']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="col">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" class="custom-input <?= !empty($errors['confirm_password']) ? 'is-invalid' : ''; ?>" placeholder="Confirm Password">
                    <?php if (!empty($errors['confirm_password'])): ?>
                        <div class="invalid-feedback"><?= $errors['confirm_password']; ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Submit and Cancel Buttons -->
            <div class="row mb-3 p-3">
                <div class="col-md-6 d-flex justify-content-center mb-3">
                    <input type="submit" class="btn btn-primary rounded-0 d-flex custom-btn" value="Update">
                </div>
                <div class="col-md-6 d-flex justify-content-center mb-3">
                    <a href="view_profile.php" class="btn btn-secondary rounded-0 d-flex custom-btn d-flex justify-content-center">Cancel</a>
                </div>
            </div>
        </form>
    </div>
<?php include_once "footer.php"; ?>    
</body>
