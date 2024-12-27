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

// Check if user is logged in and has user privileges
if (!isset($_SESSION['loggedin']) || $_SESSION['type'] !== 'user') {
    header("Location: login.php");
    exit();
}

// Retrieve the scientific_name from the URL
if (isset($_GET['scientific_name'])) {
    $scientific_name = urldecode($_GET['scientific_name']);
} else {
    // If no scientific name is provided, redirect back to the contributions page
    header("Location: contribute.php");
    exit();
}

// Include the database connection file
include 'connection.php';

// Query the database to get the specific plant data
$stmt = $conn->prepare("SELECT * FROM plant_table WHERE scientific_name = ?");
$stmt->bind_param("s", $scientific_name);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the plant data
$plant_data = $result->fetch_assoc();

// Redirect if the plant is not found
if (empty($plant_data)) {
    header("Location: contribute.php");
    exit();
}

// Close statement and connection
$stmt->close();
$conn->close();
?>

<?php include_once 'head.php'; ?>
<body>
<?php include_once 'header.php'; ?>

<div class="container detail-container mb-5">
    <h1 class="d-flex justify-content-center mb-5">Plant Details</h1>
    <div class="card mb-4">
        <?php
        $image_path = 'img/plants/' . $plant_data['plants_image']; // Path to the image
        ?>
        <img src="<?php echo $image_path; ?>" class="card-img-top contribute-card-img" alt="Herbarium Photo">
        <div class="card-body">
            <h5 class="card-title"><?php echo $plant_data['scientific_name']; ?></h5>
            <p class="card-text"><strong>Common Name:</strong> <?php echo $plant_data['common_name']; ?></p>
            <p class="card-text"><strong>Family:</strong> <?php echo $plant_data['family']; ?></p>
            <p class="card-text"><strong>Genus:</strong> <?php echo $plant_data['genus']; ?></p>
            <p class="card-text"><strong>Species:</strong> <?php echo $plant_data['species']; ?></p>
            <p class="card-text"><strong>Status:</strong> <?php echo ucfirst($plant_data['status']); ?></p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 d-flex justify-content-center">
            <div class="btn-group">
                <!-- Existing Download Button for Generated PDF -->
                <a href="download.php?scientific_name=<?php echo urlencode($plant_data['scientific_name']); ?>" class="btn btn-success me-3">Download Generated PDF</a>
                <?php
                $description_path = 'plants_description/' . $plant_data['scientific_name'] . '.pdf'; // Construct the expected file path
                if (file_exists($description_path)): // Check if the file exists
                ?>
                    <a href="<?php echo $description_path; ?>" class="btn btn-primary me-3" download>Download Uploaded PDF</a>
                <?php else: ?>
                    <button class="btn btn-secondary me-3" disabled>No Uploaded PDF Available</button>
                <?php endif; ?>

                <a href="contribute.php" class="btn btn-secondary">Back to Contributions</a>
            </div>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
</body>
</html>
