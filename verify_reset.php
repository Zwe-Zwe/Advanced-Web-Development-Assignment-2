<?php
session_name('Zwe_Het_Zaw');
session_start();
$error_message = '';

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
    // Retrieve hometown and date of birth from the form
    $input_hometown = isset($_POST['hometown']) ? trim($_POST['hometown']) : '';
    $input_dob = isset($_POST['dob']) ? trim($_POST['dob']) : '';

    // Get user email from session
    $user_email = $_SESSION['user_data']['email'] ?? '';

    if (!empty($user_email)) {
        // Fetch the user data from the database
        $sql = "SELECT hometown, dob FROM user_table WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            // Bind the email parameter
            $stmt->bind_param("s", $user_email);

            // Execute the query
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $user_data = $result->fetch_assoc();
                $stored_hometown = $user_data['hometown'] ?? '';
                $stored_dob = $user_data['dob'] ?? '';

                // Verify hometown and date of birth
                if ($input_hometown === $stored_hometown && $input_dob === $stored_dob) {
                    // Redirect to reset password page
                    header("Location: reset_password.php");
                    exit();
                } else {
                    $error_message = "Invalid hometown or date of birth.";
                }
            } else {
                $error_message = "User not found in the database.";
            }

            $stmt->close();
        } else {
            $error_message = "Error: Unable to prepare the SQL query.";
        }
    } else {
        $error_message = "Session expired or user not found.";
    }
}

$conn->close();
?>

<?php include_once "head.php"; ?>
<body id="reg-body">
    <?php include_once "header.php"; ?>
    <div class="container d-flex justify-content-center" id="forgetPassword">
        <form method="POST" action="verify_reset.php" id="login-form">
            <legend class="text-center display-7 ml-3 mb-3 mt-3">Verify Account</legend>
            <?php if (!empty($error_message)) : ?>
                <div style="color:red;"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <div class="row mb-3 p-3">
                <div class="col">
                    <input type="text" name="hometown" class="custom-input" placeholder="Enter your hometown" required>
                </div>
            </div>
            <div class="row mb-3 p-3">
                <div class="col">
                    <input type="date" name="dob" class="custom-input" required>
                </div>
            </div>  
            <div class="row mb-3 p-3">
                <div class="col d-flex justify-content-center">
                    <input type="submit" class="btn btn-primary rounded-0 d-flex custom-btn" value="Verify">
                </div>
            </div>
        </form>
    </div>
    <?php include_once 'footer.php' ?>
</body>
</html>
