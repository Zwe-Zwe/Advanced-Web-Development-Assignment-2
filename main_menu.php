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
?>
<?php include_once "head.php" ?>
<body id="menu-body">
    <?php include_once "header.php" ?>
    <div class="container" id="card-container">
        <!-- Plants Classification -->
        <div class="row card-row mt-5">
            <div class="col-12">
                <div class="card rounded-0 border-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <img src="img/Plants Classification.jpg" alt="Card" class="card-img-left img-fluid" id="card-img-1">
                        </div>
                        <div class="col-sm-8">
                            <div class="card-body">
                                <h2 class="card-title">Plants Classification</h2>
                                <p class="card-text mt-4">This page educates users about plant classification, including detailed discussions on plant family, genus, and species. By exploring the taxonomy of plants, you will gain a better understanding of how plants are categorized based on their physical and genetic characteristics. Knowing the family, genus, and species helps in identifying plants and understanding their evolutionary relationships. Whether you are a botany enthusiast or just curious about nature, this section will deepen your knowledge of the plant kingdom.</p>
                            </div>
                            <div class="d-flex justify-content-center mb-4 mt-4">
                                <a href="classify.php" class="btn btn-primary rounded-0">Go to Page</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tutorial -->
        <div class="row card-row mt-5">
            <div class="col-12">
                <div class="card rounded-0 border-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <img src="img/tutorials/glue_the_specimen1.jpg" alt="Card" class="card-img-left img-fluid" id="card-img-1">
                        </div>
                        <div class="col-sm-8">
                            <div class="card-body">
                                <h2 class="card-title">Tutorial: Herbarium Specimens</h2>
                                <p class="card-text mt-4">This tutorial provides step-by-step instructions on how to transfer a fresh leaf into herbarium specimens, an essential practice for botanical research and conservation. Learn about the tools you need, from plant presses to archival paper, as well as the proper techniques to preserve the specimens for long-term study. Additionally, discover how to maintain a herbarium, ensuring that your collected specimens remain in pristine condition for scientific reference and educational purposes. Master these techniques to contribute valuable data to plant research and taxonomy efforts.</p>
                            </div>
                            <div class="d-flex justify-content-center mb-4 mt-4">
                                <a href="tutorial.php" class="btn btn-primary rounded-0">Go to Page</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Identify -->
        <div class="row card-row mt-5">
            <div class="col-12">
                <div class="card rounded-0 border-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <img src="img/identify-plant.jpg" alt="Card" class="card-img-left img-fluid" id="card-img-1">
                        </div>
                        <div class="col-sm-8">
                            <div class="card-body">
                                <h2 class="card-title">Identify Plants</h2>
                                <p class="card-text mt-4">This page allows users to identify a plant based on the uploaded photo. With advanced image recognition technology, the system will analyze the plant photo and provide key information, including the scientific plant name, common name, and images of similar herbarium specimens. The identification process is simple, allowing you to quickly classify plants from your own surroundings. Additionally, you can download a detailed description of the plant in PDF format for future reference, making it easy to share and study the species you encounter.</p>
                            </div>
                            <div class="d-flex justify-content-center mb-4 mt-4">
                                <a href="identify.php" class="btn btn-primary rounded-0">Go to Page</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contribution -->
        <div class="row card-row mt-5"> 
            <div class="col-12">
                <div class="card rounded-0 border-0">
                    <div class="row">
                        <div class="col-sm-4">
                            <img src="img/DataContribution.jpg" alt="Card" class="card-img-left img-fluid" id="card-img-1">
                        </div>
                        <div class="col-sm-8">
                            <div class="card-body">
                                <h2 class="card-title">Contribute to the Database</h2>
                                <p class="card-text mt-4">This page invites users to contribute to our plant database by uploading photos of fresh leaves or herbarium specimens, along with relevant plant information. Your contribution helps expand the dataset and aids in further research on plant identification and classification. By filling out the form with detailed information about the plant, you provide valuable data for the “Identify” page and other users. All submitted photos and data are reviewed and stored in our database, ensuring that the plant information is accurate and up-to-date for future reference.</p>
                            </div>
                            <div class="d-flex justify-content-center mb-4 mt-4">
                                <a href="contribute.php" class="btn btn-primary rounded-0">Go to Page</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php include_once "footer.php" ?> 
</body>
</html>
