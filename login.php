<?php
session_name('Zwe_Het_Zaw');
session_start();  // Start the session to manage user state

// Redirect to index.php if the user is already logged in
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header("Location: index.php");
    exit;
}

include_once 'connection.php'; // Include database connection

// Initialize an error message variable
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // Query to check the user's credentials in the account_table
    $sql = "SELECT u.gender, u.profile_image, a.password, a.type 
            FROM user_table u 
            JOIN account_table a ON u.email = a.email 
            WHERE a.email = ?";

    // Prepare the query to prevent SQL injection
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if a matching email was found
    if ($stmt->num_rows > 0) {
        // Bind result variables
        $stmt->bind_result($user_gender, $user_profile_image, $stored_password, $user_type);
        $stmt->fetch();

        // Check if the provided password matches the stored hashed password
        if (password_verify($password, $stored_password)) {  // Use password_verify to check the hash
            // Store the email, gender, profile image, and type in session
            $_SESSION['loggedin'] = true;
            $_SESSION['email'] = $email;
            $_SESSION['gender'] = $user_gender; // Store gender in the session
            $_SESSION['profile_image'] = $user_profile_image; // Store profile image in session
            $_SESSION['type'] = $user_type; // Store user type in session

            // Redirect based on user type (admin or user)
            if ($user_type === 'admin') {
                header("Location: main_menu_admin.php");  // Redirect to the admin main menu
            } else {
                header("Location: main_menu.php");  // Redirect to the regular user main menu
            }
            exit;
        } else {
            // Incorrect password
            $error_message = "Invalid email or password!";
        }
    } else {
        // Email not found
        $error_message = "Invalid email or password!";
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!-- HTML section of the login page -->
<?php include_once "head.php"; ?>
<body id="reg-body">
    <?php include_once "header.php"; ?>
    <div class="container d-flex justify-content-center" id="login-container">
        <form id="login-form" method="POST" action="login.php">
            <legend class="text-center display-7 ml-3 mb-3 mt-3">Please Login</legend>

            <!-- Display error message if login failed -->
            <?php if (!empty($error_message)) : ?>
                <div style="color:red; text-align:center;"><?= htmlspecialchars($error_message); ?></div>
            <?php endif; ?>

            <!-- Email Input Field -->
            <div class="row mb-3 p-3">
                <div class="col">
                    <input type="email" name="email" class="custom-input" placeholder="Email" required>
                </div>
            </div>

            <!-- Password Input Field -->
            <div class="row mb-3 p-3">
                <div class="col">
                    <input type="password" name="password" class="custom-input" placeholder="Password" required>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="row mb-3 p-3">
                <div class="col d-flex justify-content-center">
                    <input type="submit" class="btn btn-primary rounded-0 d-flex custom-btn" value="Login">
                </div>
            </div>

            <!-- Registration Link -->
            <div class="row">
                <div class="col d-flex justify-content-center">
                    <p>Don't have an account? <a class="login-a" href="registration.php">Register</a></p>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col d-flex justify-content-center">
                    <p><a class="login-a" href="forget_password.php">Forgot the password?</a></p>
                </div>
            </div>
        </form>
    </div>
    <?php include_once 'footer.php' ?>
</body>
</html>
