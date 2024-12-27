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

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}

include_once 'connection.php'; // Include your database connection

// Initialize an error message variable
$error_message = '';

// Function to retrieve user data by email from the database
function getUserByEmail($email, $conn) {
    // Query to fetch user details from the database (excluding gender)
    $sql = "SELECT u.first_name, u.last_name, u.student_id, u.profile_image, u.email
            FROM user_table u
            JOIN account_table a ON u.email = a.email
            WHERE u.email = ?";
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die('Error preparing statement: ' . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    // Declare variables to store the fetched result
    $first_name = '';
    $last_name = '';
    $student_id = '';
    $profile_image = '';
    $email = '';

    // Check if a user exists with this email
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($first_name, $last_name, $student_id, $profile_image, $email);
        $stmt->fetch();
        
        // Return user data as an associative array (without gender)
        return [
            'First Name' => $first_name,
            'Last Name' => $last_name,
            'Student ID' => $student_id,
            'Profile Image' => $profile_image,
            'Email' => $email
        ];
    } else {
        return null; // Return null if user not found
    }
}



// Check if the user is logged in (email should be stored in the session after login)
if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    // Retrieve user data by email from the database
    $user_data = getUserByEmail($email, $conn);

    if ($user_data) {
        ?>
        <?php include_once "head.php"; ?>
        <body>
        <?php include_once "header.php"; ?>
        <div class="container profile-container">
            <div class="row justify-content-center profile-row">
                <div class="col-md-6 col-lg-5">

                    <!-- Profile Card -->
                    <div class="card profile-card">
                        <div class="card-header profile-card-header text-center">
                            <h2>Profile Page</h2>
                        </div>
                        <div class="card-body text-center profile-card-body">

                            <!-- Profile Image -->
                            <?php if (!empty($user_data['Profile Image']) && file_exists("img/profile_images/" . $user_data['Profile Image'])) : ?>
                                <img src="img/profile_images/<?php echo htmlspecialchars($user_data['Profile Image']); ?>" class="img-fluid rounded-circle mb-3" alt="Profile Image" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php else : ?>
                                <img src="img/profile_images/default.png" class="img-fluid rounded-circle mb-3" alt="Profile Image" style="width: 150px; height: 150px; object-fit: cover;">
                            <?php endif; ?>

                            <!-- Name and Info -->
                            <div>
                                <p><strong>Name:</strong> <?php echo htmlspecialchars($user_data['First Name'] . ' ' . $user_data['Last Name']); ?></p>
                                <p><strong>Student ID:</strong> <?php echo htmlspecialchars($user_data['Student ID'] ?? 'N/A'); ?></p>
                                <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($user_data['Email']); ?>"><?php echo htmlspecialchars($user_data['Email']); ?></a></p>
                            </div>
                        </div>

                        <!-- Declaration -->
                        <div class="card-footer text-center profile-card-footer">
                            <p class="mb-3 text-justify">
                                I declare that this assignment is my individual work. I have not worked collaboratively nor have I copied from any other student's work or from any other source. I have not engaged another party to complete this assignment. I am aware of the University’s policy with regards to plagiarism. I have not allowed, and will not allow, anyone to copy my work with the intention of passing it off as his or her own work.
                            </p>

                            <!-- Update Profile Button -->
                            <div>
                                <a href="update_profile.php" class="btn btn-secondary btn-sm">Edit Profile</a>
                                <a href="index.php" class="btn btn-secondary btn-sm">Home Page</a>
                                <a href="about.php" class="btn btn-secondary btn-sm">About</a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <?php include_once "footer.php"; ?>
        </body>
        </html>
        <?php
    } else {
        echo "<p class='text-center mt-5'>No profile found for this email. Please register first.</p>";
    }
} else {
    echo "<p class='text-center mt-5'>You must be logged in to view your profile. Please <a href='login.php'>log in</a>.</p>";
}

// Close the connection
$conn->close();
?>
