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

// Include database connection (make sure this file contains the connection to your database)
include_once 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    // Check if the email exists in the database
    $sql = "SELECT u.email, u.first_name, u.last_name, u.dob, u.gender, u.contact_number, u.hometown, u.profile_image, a.password, a.type
            FROM user_table u
            JOIN account_table a ON u.email = a.email
            WHERE u.email = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        // Bind the email parameter
        $stmt->bind_param("s", $email);

        // Execute the query
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // User found, fetch the data
            $user_data = $result->fetch_assoc();
            $_SESSION['user_data'] = $user_data; // Store the user's data in session

            // Redirect to the OTP verification page
            header("Location: verify_reset.php");
            exit();
        } else {
            $error_message = "No account found with that email address.";
        }

        $stmt->close();
    } else {
        $error_message = "Error: Unable to prepare the SQL query.";
    }
}

$conn->close();
?>

<?php include_once "head.php"; ?>
<body id="reg-body">
    <?php include_once "header.php"; ?>
    <div class="container d-flex justify-content-center" id="forgetPassword">
        <form method="POST" action="forget_password.php" id="login-form">
            <legend class="text-center display-7 ml-3 mb-3 mt-3">Forgot Password</legend>
            <?php if (!empty($error_message)) : ?>
                <div style="color:red;"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            <div class="row mb-3 p-3">
                <div class="col">
                    <input type="text" name="email" class="custom-input" placeholder="Enter your email" required>
                </div>
            </div>  
            <div class="row mb-3 p-3">
                <div class="col d-flex justify-content-center">
                    <input type="submit" class="btn btn-primary rounded-0 d-flex custom-btn" value="Check Email">
                </div>
            </div>
        </form>
    </div>
    <?php include_once 'footer.php' ?>
</body>
</html>
