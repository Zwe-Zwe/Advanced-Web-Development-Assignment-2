<?php
session_name('Zwe_Het_Zaw');
session_start();
include_once 'connection.php';

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

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['loggedin']) || $_SESSION['type'] !== 'admin') {
    header("Location: login.php");
    exit();
}
// Query to fetch all users
$sql = "SELECT user_table.email, user_table.first_name, user_table.last_name, user_table.contact_number AS phone_number, user_table.student_id, user_table.gender, user_table.dob, user_table.hometown, user_table.profile_image 
        FROM user_table 
        JOIN account_table ON user_table.email = account_table.email";

$result = $conn->query($sql);
// Handle Add User (Submit Button)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    // Extract data from form submission
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $student_id = $_POST['student_id'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob']; 
    $hometown = $_POST['hometown']; 
    $profile_img = ($gender === 'Male') ? 'boys.jpg' : (($gender === 'Female') ? 'girl.png' : 'default.png');
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Validate password
    if (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Password and Confirm Password do not match.";
    }

    // Validate other fields
    if (empty($first_name)) { $errors['first_name'] = "First name is required."; }
    if (empty($last_name)) { $errors['last_name'] = "Last name is required."; }
    if (empty($dob)) { $errors['dob'] = "Date of Birth is required."; }
    if (empty($email)) { $errors['email'] = "Email is required."; }
    if (empty($phone_number)) { $errors['phone_number'] = "Phone number is required."; }
    if (empty($student_id)) { $errors['student_id'] = "Student ID is required."; }

    // If no errors, insert new user and account
    if (empty($errors)) {
        // Handle profile image upload
        $image_name = $profile_img;
        // Insert user into the user_table
        $insert_user_sql = "INSERT INTO user_table (email, first_name, last_name, contact_number, student_id, gender, dob, hometown, profile_image) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        // Hash password before inserting into account_table
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $insert_account_sql = "INSERT INTO account_table (email, password) VALUES (?, ?)";

        $conn->begin_transaction(); // Start a transaction

        try {
            // Prepare and execute insert queries
            $stmt = $conn->prepare($insert_user_sql);
            $stmt->bind_param("sssssssss", $email, $first_name, $last_name, $phone_number, $student_id, $gender, $dob, $hometown, $image_name);
            $stmt->execute();

            $stmt = $conn->prepare($insert_account_sql);
            $stmt->bind_param("ss", $email, $hashed_password);
            $stmt->execute();

            $conn->commit(); // Commit transaction

            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } catch (Exception $e) {
            $conn->rollback(); // Rollback transaction on error
            echo "Error: " . $e->getMessage();
        }
    } else {
        // Handle errors (display to user)
        echo "<div class='alert alert-danger'>" . implode('<br>', $errors) . "</div>";
    }
}
function handleImageUpload($imageFile, $defaultImage) {
    $targetDir = "img/profile_images/"; // Directory to store uploaded images
    $imageName = $defaultImage; // Default to NULL if no image is uploaded

    if (isset($imageFile) && $imageFile['error'] == 0) {
        $imageName = basename($imageFile['name']);
        $targetFilePath = $targetDir . $imageName;
        $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        // Validate file type
        $validFileTypes = ['jpg', 'jpeg', 'png'];
        if (!in_array($imageFileType, $validFileTypes)) {
            throw new Exception("Invalid file type. Only JPG, JPEG, PNG files are allowed.");
        }

        // Validate file size
        if ($imageFile['size'] > 5 * 1024 * 1024) {
            throw new Exception("File size exceeds the maximum limit of 5MB.");
        }
        
        // Move the uploaded file
        if (!move_uploaded_file($imageFile['tmp_name'], $targetFilePath)) {
            throw new Exception("Failed to upload the image. Please try again.");
        }
    }

    return $imageName;
}
// Handle Edit User (Upgrade Button)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    // Extract data from form submission
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $student_id = $_POST['student_id'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob']; 
    $hometown = $_POST['hometown']; 
    $profile_image = $_FILES['profile_image']; 
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $confirm_password = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';
    $errors = [];

    // Validate new password if provided
    if (!empty($password) && strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters long.";
    }
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Password and Confirm Password do not match.";
    }

    // Validate other fields
    if (empty($first_name)) { $errors['first_name'] = "First name cannot be empty."; }
    if (empty($last_name)) { $errors['last_name'] = "Last name cannot be empty."; }
    if (empty($dob)) { $errors['dob'] = "Date of Birth cannot be empty."; }
    if (empty($email)) { $errors['email'] = "Email cannot be empty."; }
    if (empty($hometown)) { $errors['hometown'] = "Hometown cannot be empty."; }
    if (empty($phone_number)) { $errors['phone_number'] = "Phone number cannot be empty."; }
    if (empty($student_id)) { $errors['student_id'] = "Student ID cannot be empty."; }

    if (empty($errors)) {
        // Handle Profile Image Upload
        try {
            $image_name = handleImageUpload($_FILES['profile_image'], $defaultImage = NULL);
            $_SESSION['profile_image'] = $image_name;
        } catch (Exception $e) {
            $errors['profile_image'] = $e->getMessage();
        }

        // Prepare update SQL for user details
        $update_sql = "UPDATE user_table SET first_name=?, last_name=?, contact_number=?, student_id=?, gender=?, dob=?, hometown=?, profile_image=? WHERE email=?";
        
        // If password is provided, hash it and update in account_table
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_password_sql = "UPDATE account_table SET password=? WHERE email=?";
            $stmt = $conn->prepare($update_password_sql);
            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();
        }

        // Update user details in user_table
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssssssss", $first_name, $last_name, $phone_number, $student_id, $gender, $dob, $hometown, $image_name, $email);
        if ($stmt->execute()) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Error updating user.";
        }
    } else {
        // Handle errors (display them as alerts)
        echo "<div class='alert alert-danger'>" . implode('<br>', $errors) . "</div>";
    }
}
// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $email = $_POST['email'];

    $delete_sql = "DELETE FROM user_table WHERE email=?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("s", $email);
    if ($stmt->execute()) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        echo "Error deleting user.";
    }
}
?>
<?php include_once "head.php"; ?>
<body>
<?php include_once "header.php"; ?>
<h2 class="text-center table-heading user-table-heading">User Accounts Management</h2>
<!-- Add User Button -->
<div class="d-flex justify-content-center mb-3">
    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
</div>
<!-- Modal for Adding User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">Create an Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reg-form" method="POST" enctype="multipart/form-data">
                    <legend class="text-center display-7 mb-3 mt-3">Create an account</legend>
                    <div class="row mb-3 p-3">
                        <div class="col-md-6 mb-3 input-container">
                            <input type="text" name="first_name" id="first-name" class="form-control" placeholder="First Name" value="<?= htmlspecialchars($form_data['first_name'] ?? '') ?>">
                            <?php if (isset($errors['first_name'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['first_name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 input-container">
                            <input type="text" name="last_name" id="last-name" class="form-control" placeholder="Last Name" value="<?= htmlspecialchars($form_data['last_name'] ?? '') ?>">
                            <?php if (isset($errors['last_name'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['last_name']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3 p-3">
                        <div class="col-md-6 mb-3 input-container">
                            <label class="mb-3" for="date-of-birth">Date of Birth: </label>
                            <input type="date" name="dob" class="form-control" id="date-of-birth" value="<?= htmlspecialchars($form_data['dob'] ?? '') ?>">
                            <?php if (isset($errors['dob'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['dob']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 input-container">
                            <p class="mb-4">Gender: </p>
                            <label class="form-check-label" for="male">Male</label>
                            <input class="form-check-input" type="radio" name="gender" id="male" value="Male" <?= (isset($form_data['gender']) && $form_data['gender'] === 'Male') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="female">Female</label>
                            <input class="form-check-input" type="radio" name="gender" id="female" value="Female" <?= (isset($form_data['gender']) && $form_data['gender'] === 'Female') ? 'checked' : '' ?>>
                            <?php if (isset($errors['gender'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['gender']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3 p-3">
                        <div class="col-md-6 mb-3 input-container">
                            <input type="text" name="email" id="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($form_data['email'] ?? '') ?>">
                            <?php if (isset($errors['email'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['email']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 input-container">
                            <input type="text" name="hometown" id="hometown" class="form-control" placeholder="Home Town" value="<?= htmlspecialchars($form_data['hometown'] ?? '') ?>">
                            <?php if (isset($errors['hometown'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['hometown']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3 p-3">
                        <div class="col-md-6 mb-3 input-container">
                            <input type="text" name="phone_number" id="phone-number" class="form-control" placeholder="Phone Number" value="<?= htmlspecialchars($form_data['phone_number'] ?? '') ?>">
                            <?php if (isset($errors['phone_number'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['phone_number']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 input-container">
                            <input type="text" name="student_id" id="student-id" class="form-control" placeholder="Student ID" value="<?= htmlspecialchars($form_data['student_id'] ?? '') ?>">
                            <?php if (isset($errors['student_id'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['student_id']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="row mb-3 p-3">
                        <div class="col-md-6 mb-3 input-container">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                            <?php if (isset($errors['password'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['password']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 input-container">
                            <input type="password" name="confirm_password" id="confirm-password" class="form-control" placeholder="Confirm Password">
                            <?php if (isset($errors['confirm_password'])): ?>
                                <p class="error-message"><?= htmlspecialchars($errors['confirm_password']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" name="add_user" class="btn btn-primary">Submit</button>
                        <button type="reset" class="btn btn-secondary" onclick="clearErrors()">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container user-table-container">
    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive user-table-responsive">
            <table class="table table-hover table-bordered shadow-sm user-table">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Student ID</th>
                        <th>Gender</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $counter ?></td>
                            <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $counter ?>">Edit</button>
                                <form method="POST" action="" class="d-inline">
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($row['email']) ?>" />
                                    <button type="submit" name="delete_user" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal content for editing user -->
                        <div class="modal fade" id="editModal<?= $counter ?>" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit User</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form method="POST" enctype="multipart/form-data">
                                            <input type="hidden" name="email" value="<?= htmlspecialchars($row['email']) ?>" />
                                            <div class="form-group">
                                                <label for="first_name">First Name</label>
                                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= htmlspecialchars($row['first_name']) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="last_name">Last Name</label>
                                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= htmlspecialchars($row['last_name']) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone_number">Phone Number</label>
                                                <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?= htmlspecialchars($row['phone_number']) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="student_id">Student ID</label>
                                                <input type="text" class="form-control" id="student_id" name="student_id" value="<?= htmlspecialchars($row['student_id']) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="gender">Gender</label>
                                                <select class="form-control" id="gender" name="gender">
                                                    <option value="Male" <?= $row['gender'] == 'Male' ? 'selected' : '' ?>>Male</option>
                                                    <option value="Female" <?= $row['gender'] == 'Female' ? 'selected' : '' ?>>Female</option>
                                                    <option value="Other" <?= $row['gender'] == 'Other' ? 'selected' : '' ?>>Other</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="dob">Date of Birth</label>
                                                <input type="date" class="form-control" id="dob" name="dob" value="<?= htmlspecialchars($row['dob']) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="hometown">Hometown</label>
                                                <input type="text" class="form-control" id="hometown" name="hometown" value="<?= htmlspecialchars($row['hometown']) ?>" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="profile_image">Profile Image</label>
                                                <input type="file" class="form-control" id="profile_image" name="profile_image">
                                            </div>
                                            <div class="form-group">
                                                <label for="password">New Password</label>
                                                <input type="password" class="form-control" id="password" name="password">
                                            </div>
                                            <div class="form-group">
                                                <label for="confirm_password">Confirm New Password</label>
                                                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                                            </div>
                                            <button type="submit" name="edit_user" class="btn btn-primary mt-3">Save Changes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php $counter++; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No users found.</p>
    <?php endif; ?>
</div>
<?php include_once "footer.php"; ?>
</body>
</html>
