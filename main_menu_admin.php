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

// Check if user is logged in and has admin privileges
if (!isset($_SESSION['loggedin']) || $_SESSION['type'] !== 'admin') {
    header("Location: login.php");
    exit();
}
?>
<?php include_once "head.php" ?>
<body id="menu-body">
    <?php include_once "header.php" ?>
    
    <!-- Main container that takes full height -->
    <div class="page-container">
        <!-- Centered container for the cards -->
        <div class="centered-container">
            <div class="admin-cards">
                <!-- Card for managing accounts -->
                <a href="manage_accounts.php" class="admin-card admin-card-primary">
                    <h3>Manage Accounts</h3>
                </a>
                
                <!-- Card for managing plants -->
                <a href="manage_plants.php" class="admin-card admin-card-success">
                    <h3>Manage Plants</h3>
                </a>
            </div>
        </div>
    </div>
    
    <?php include_once "footer.php" ?> 
</body>
</html>
