<?php 
session_name('Zwe_Het_Zaw');
session_start(); 
require_once 'main.php';

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
?>

<?php include_once "head.php" ?>
<body id="index-body">
    <?php include_once "header.php" ?>
    <article id="index-article">
        <div class="encircle bounce animated">
            <div class="arrow"></div>
        </div>
        <div class="slide-container">
            <div class="slide">
                <img src="img/Thismia-mantiqueirensis.jpeg" class="slide-images" alt="Thismia-mantiqueirensis">
                <div class="slide-content">
                    <h1>Welcome To The Herbarium Project</h1>
                    <a href="login.php" class="btn btn-primary mb-3 p-2 px-5 index-button">Login</a>
                    <a href="registration.php" class="btn btn-success mb-3 p-2 px-5 index-button">Register</a>
                </div>
            </div>
            <div class="slide">
                <img src="img/Purple-Alpine-Paintbrush.jpg" class="slide-images" alt="Purple-Alpine-Paintbrush">
                <div class="slide-content">
                    <h1>Welcome To The Herbarium Project</h1>
                    <a href="login.php" class="btn btn-primary mb-3 p-2 px-5 index-button">Login</a>
                    <a href="registration.php" class="btn btn-success mb-3 p-2 px-5 index-button">Register</a>
                </div>
            </div>
            <div class="slide">
                <img src="img/Nitella-praeclara.jpg" class="slide-images" alt="Nitella-praeclara">
                <div class="slide-content">
                    <h1>Welcome To The Herbarium Project</h1>
                    <a href="login.php" class="btn btn-primary mb-3 p-2 px-5 index-button">Login</a>
                    <a href="registration.php" class="btn btn-success mb-3 p-2 px-5 index-button">Register</a>
                </div>
            </div>
        </div>
        <div class="index-content">
            <div class="index-content-1-container">
                <div class="index-content-1">
                    <div class="index-content-1-text">
                        <h2>The Project</h2>
                        <p>The "Herbarium for Plant Biodiversity" project enables participants to contribute plant specimen photos and data to support biodiversity research. By digitizing herbarium collections, the platform helps researchers compare and identify species, preserving valuable plant information for future study and conservation efforts.</p>
                    </div>
                </div>
                <div class="index-content-1">
                    <img src="img/Tiputinia.jpg" alt="Tiputinia" class="index-content-1-photo">
                </div>
            </div>
            <div class="horizontal-line"></div>
            <div class="index-content-2-container">
                <div class="index-content-2 order-1 order-md-2"> <!-- Add order classes -->
                    <div class="index-content-2-text">
                        <blockquote>"A herbarium is better than any illustration; every botanist should make one."</blockquote>
                        <cite>- Carolus Linnaeus </cite>
                    </div>
                </div>
                <div class="index-content-2 order-2 order-md-1"> <!-- Add order classes -->
                    <img src="img/Oregon-Beaked-Moss.jpeg" alt="Oregon-Beaked-Moss" class="index-content-2-photo">
                </div>
            </div>
        </div>
        
        <section class="py-12 mb-5">
            <div class="container">
                <h2 class="text-center mb-5">Herbarium Specimens</h2>
                <div class="row justify-content-center">
                    <?php
                    // Get all images from the 'img/plants' directory
                    $images = glob("img/plants/*.{jpg,jpeg,png}", GLOB_BRACE);

                    // Check if there are any images
                    if (!empty($images)) {
                        // Shuffle the images to randomize
                        shuffle($images);
                        
                        // Select the first 6 random images
                        $random_images = array_slice($images, 0, 6);

                        // Loop through the selected images and display them
                        foreach ($random_images as $image) {
                            $image_name = basename($image); // Get the image file name

                            echo "<div class='col-6 col-md-6 col-lg-4 p-3 d-flex justify-content-center'>";
                            echo "<img src='{$image}' alt='{$image_name}' class='img-fluid rounded shadow herbarium-img' />";
                            echo "</div>";
                        }
                    } else {
                        // If no images found
                        echo "<p class='text-center'>No herbarium specimens available at the moment. Please check back later.</p>";
                    }
                    ?>
                </div>
            </div>
        </section>
    </article>
    
<?php include_once "footer.php" ?>
</body>
</html>