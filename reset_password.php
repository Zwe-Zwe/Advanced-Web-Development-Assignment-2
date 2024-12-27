<?php
session_name('Zwe_Het_Zaw');
session_start();
$error_message = '';
$success_message = '';

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

// Include the database connection
include_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_password = isset($_POST['new_password']) ? trim($_POST['new_password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';

    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Get user email from session
        $user_email = $_SESSION['user_data']['email'] ?? '';

        if (!empty($user_email)) {
            // Hash the new password before storing it
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Update the password in the database
            $sql = "UPDATE account_table SET password = ? WHERE email = ?";

            if ($stmt = $conn->prepare($sql)) {
                // Bind the parameters
                $stmt->bind_param("ss", $hashed_password, $user_email);

                // Execute the query
                if ($stmt->execute()) {
                    // Clear session data
                    session_destroy();

                    // Redirect to the login page with a success message
                    header("Location: login.php?message=Password updated successfully.");
                    exit();
                } else {
                    $error_message = "Error updating the password.";
                }

                $stmt->close();
            } else {
                $error_message = "Error preparing the SQL query.";
            }
        } else {
            $error_message = "Session expired or user not found.";
        }
    }
}

$conn->close();
?>

<?php include_once "head.php"; ?>
<body id="reg-body">
    <?php include_once "header.php"; ?>
    <div class="container d-flex justify-content-center" id="forgetPassword">
        <form method="POST" action="reset_password.php" id="login-form">
            <legend class="text-center display-7 ml-3 mb-3 mt-3">Reset Password</legend>
            <?php if (!empty($error_message)) : ?>
                <div style="color:red;"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <div class="row mb-3 p-3">
                <div class="col">
                    <input type="password" name="new_password" class="custom-input" placeholder="Enter new password" required>
                </div>
            </div>
            <div class="row mb-3 p-3">
                <div class="col">
                    <input type="password" name="confirm_password" class="custom-input" placeholder="Confirm new password" required>
                </div>
            </div>
            <div class="row mb-3 p-3">
                <div class="col d-flex justify-content-center">
                    <input type="submit" class="btn btn-primary rounded-0 d-flex custom-btn" value="Reset Password">
                </div>
            </div>
        </form>
    </div>
    <?php include_once 'footer.php' ?>
</body>
</html>
