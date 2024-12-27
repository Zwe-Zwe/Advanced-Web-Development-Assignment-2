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
<body class="tutorial-body">
    <?php include_once "header.php" ?>
    <div class="container mt-4 mb-4 tutorial-container">
        <!-- Top Image -->
        <div class="text-center mb-5 tutorial-top-container">
            <img src="img/tutorials/specimen.jpg" alt="Herbarium Specimen" class="img-fluid rounded shadow tutorial-top-img">
        </div>

        <h2 class="text-center mb-5">How to Make Herbarium Specimens</h2>

            <!-- Step 1: Collecting -->
            <section id="collecting" class="mt-5 mb-5">
                <div class="row mb-5">
                    <div class="col-md-6 order-1 order-md-1">
                        <h3 class="mb-4 tutorial-title">Step 1: Collecting</h3>
                        <!-- Where to Collect -->
                        <h5 class="mt-3 tutorial-title">Where to Collect</h5>
                        <p>
                        The best places to collect specimens are along edges, pathways, field and woodland borders, fence lines, and railways. These areas offer the most diversity while providing protection from frequent trimming, allowing plants to thrive undisturbed. However, always be cautious not to collect from private property, nature reserves, or any areas where permission is required. Additionally, make sure to check for local guidelines or regulations to ensure that the species you're collecting are not protected or endangered. By following these practices, you can collect responsibly while preserving the natural environment for future generations.
                        </p>
                        <!-- What to Collect -->
                        <h5 class="mt-3 tutorial-title">What to Collect</h5>
                        <p>
                        Collect samples that include all parts of the plant for easier identification—especially when flowering, as flowers are often key to distinguishing species. Be sure to select healthy, pest-free, and dry specimens for pressing to avoid decay during preservation. Additionally, take the time to note down important characteristics such as smell, texture, and the surrounding growing conditions, which can further aid in accurate identification later. Don’t forget to document the location and time of year, as plants can vary based on their environment and season. Properly labeling and preserving your specimens ensures a valuable resource for future study and reference.
                        </p>
                    </div>
                    <div class="col-md-6 text-center order-2 order-md-2">
                        <img src="img/tutorials/What To Collect.jpeg" alt="Collecting Plants" class="img-fluid tutorial-img rounded shadow">
                    </div>
                </div>
            </section>

            <!-- Step 2: Preparation -->
            <section id="preparation" class="mt-5 mb-5">
                <div class="row mb-5">
                    <div class="col-md-6 text-center order-2 order-md-1">
                        <img src="img/tutorials/arranging the specimens.jpeg" alt="Preparing Plants" class="img-fluid tutorial-img rounded shadow">
                    </div>
                    <div class="col-md-6 order-1 order-md-2">
                        <h3 class="mb-4 tutorial-title">Step 2: Preparation</h3>
                        <!-- Protecting Specimens -->
                        <h5 class="mt-3 tutorial-title">Protecting the Specimens</h5>
                        <p>
                        After collecting, protect the specimens by placing them in sealable plastic bags within a sturdy satchel to retain moisture and prevent damage. Ensure the bags are tightly sealed to prevent air from drying out the samples. If pressing cannot be done immediately, some plants can be safely stored in the refrigerator for up to 24 hours, though it’s best to press them as soon as possible to preserve their quality. You can also use damp paper towels inside the bags to help maintain humidity. Proper storage prevents wilting and ensures that your specimens remain in good condition for pressing and identification.
                        </p>
                        <!-- Arranging Specimens -->
                        <h5 class="mt-3 tutorial-title">Arranging the Specimens</h5>
                        <p>
                        Arrange plants in their natural structure with minimal overlapping, ensuring that unique parts like leaf backs, flowers, or seed heads are clearly visible. Pay special attention to positioning leaves and petals to showcase important details for later identification. Use parchment paper or blotting paper for pressing to avoid discoloration, particularly in delicate blooms. Make sure to separate plants by moisture content to prevent over-moist plants from damaging drier ones during the pressing process, ensuring even and effective drying across all specimens.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Step 3: Pressing -->
            <section id="pressing" class="mt-5 mb-5">
                <div class="row mb-5">
                    <div class="col-md-6 order-1 order-md-1">
                        <h3 class="mb-4 tutorial-title">Step 3: Pressing</h3>
                        <p>
                        Load the plants into the press carefully, ensuring that parchment paper is placed between the plant and the plywood dividers to prevent direct contact and possible damage. Apply consistent, even pressure using a press with adjustable tension, or alternatively, heavy books can be used to flatten the plants while drying. After 24 hours, inspect the specimens to ensure they are drying properly and rearrange them if necessary, replacing any damp paper to avoid mold or damage. Leave the plants in the press for about a week, or longer if working with fleshier or thicker plants that require more time to fully dry and preserve.
                        </p>
                    </div>
                    <div class="col-md-6 text-center order-2 order-md-2">
                        <img src="img/tutorials/pressing the specimens.jpeg" alt="Pressing Plants" class="img-fluid tutorial-img rounded shadow">
                    </div>
                </div>
            </section>

            <!-- Step 4: Mounting -->
            <section id="mounting" class="mt-5 mb-5">
                <div class="row mb-5">
                    <div class="col-md-6 text-center order-2 order-md-1">
                        <img src="img/tutorials/mounting the specimens.jpeg" alt="Mounting Plants" class="img-fluid tutorial-img rounded shadow">
                    </div>
                    <div class="col-md-6 order-1 order-md-2">
                        <h3 class="mb-4 tutorial-title">Step 4: Mounting</h3>
                        <p>
                        Once dry, carefully mount the specimens on acid-free paper using an acrylic adhesive to ensure their long-term preservation and protection from degradation. Apply the adhesive evenly to all parts of the plant with a small brush, paying special attention to edges and stems for secure placement. Use tweezers and wooden skewers to handle the plant and avoid direct contact, which could damage delicate parts. Work meticulously and swiftly, saving fragile areas like flower petals or thin leaves for last, ensuring that they are positioned properly for the best visual and structural preservation. Allow the adhesive to dry completely before storing or displaying the specimen.
                        </p>
                    </div>
                </div>
            </section>

            <!-- Step 5: Freezing -->
            <section id="freezing" class="mt-5 mb-5">
                <div class="row mb-5">
                    <div class="col-md-6 order-1 order-md-1">
                        <h3 class="mb-4 tutorial-title">Step 5: Freezing</h3>
                        <p>
                        To eliminate any pests that may remain, freeze the mounted specimens for 72 hours to ensure thorough extermination. Before freezing, protect the specimens from moisture by wrapping them in plastic foil, preventing condensation that could damage the plants. Use adjustable belts or straps to apply gentle pressure during freezing, helping the specimens stay flat and ensuring the mounting glue fully sets. After removing from the freezer, allow the specimens to acclimate to room temperature gradually to avoid any warping or condensation buildup that could compromise their preservation.
                        </p>
                    </div>
                    <div class="col-md-6 text-center order-2 order-md-2">
                        <img src="img/tutorials/freezing the specimens.jpeg" alt="Freezing Specimens" class="img-fluid tutorial-img rounded shadow">
                    </div>
                </div>
            </section>

            <!-- Step 6: Identification -->
            <section id="identification" class="mt-5 mb-5">
                <div class="row mb-5">
                    <div class="col-md-6 text-center order-2 order-md-1">
                        <img src="img/tutorials/identifying the specimens.jpeg" alt="Identifying Plants" class="img-fluid tutorial-img rounded shadow">
                    </div>
                    <div class="col-md-6 order-1 order-md-2">
                        <h3 class="mb-4 tutorial-title">Step 6: Identification</h3>
                        <p>
                        Identification can be done either before or after pressing, but it's essential to record characteristics that may not be visible in dried specimens, such as color, scent, or growth habit. Use tools like a dichotomous key and a hand lens to examine fine details of the plant for accurate identification. Additionally, note unique traits like leaf arrangement or stem texture that may help differentiate between similar species. For further confirmation, compare the specimen with botanical illustrations, photographs, or consult herbarium references to ensure proper classification.
                        </p>
                    </div>
                </div>
            </section>

        <!-- Step 7: Cataloging and Storage -->
        <section id="cataloging" class="mt-5 mb-5">
            <div class="row">
                <div class="col-md-6 order-1 order-md-1">
                    <h3 class="mb-4 tutorial-title">Step 7: Cataloging and Storage</h3>
                    <p>
                    Assign a unique number to each plant specimen and link it to a digital database where corresponding photos and detailed information are stored for easy reference. This database can serve as a valuable resource for future research and identification. Insert the specimens into acid-free sleeves to ensure their protection and longevity, and label each sleeve with the plant's botanical and common names, as well as the collection location and date. For optimal storage, keep the specimens in wooden cases filled with cedar shavings, which help protect them from light damage and deter insects. This method ensures that your collection remains organized and well-preserved for years to come.
                    </p>
                </div>
                <div class="col-md-6 text-center order-2 order-md-2">
                    <img src="img/tutorials/preserving the specimens.jpeg" alt="Cataloging Specimens" class="img-fluid tutorial-img rounded shadow">
                </div>
            </div>
        </section>
        <h2 class="text-center mb-4">Tools You Might Need</h2>
        <p class="text-center">Before starting, gather these tools to help in preparing herbarium specimens:</p>
    
    
        <div class="container text-center">
    <!-- First Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <img src="img/tutorials/bag.jpg" alt="Bags to store specimens" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Bags to store specimens</h6>
        </div>
        <div class="col-md-4">
            <img src="img/tutorials/shovel.jpg" alt="Trowel for digging up roots" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Trowel for digging up roots</h6>
        </div>
        <div class="col-md-4">
            <img src="img/tutorials/clipper.jpg" alt="Clipper for cutting woody stems" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Clipper for cutting woody stems</h6>
        </div>
    </div>

    <!-- Second Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <img src="img/tutorials/straps.jpg" alt="Straps and cupboards for pressing and drying" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Straps and cupboards for pressing and drying</h6>
        </div>
        <div class="col-md-4">
            <img src="img/tutorials/waxPaper.webp" alt="Wax paper for mounting plants" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Wax paper for mounting plants</h6>
        </div>
        <div class="col-md-4">
            <img src="img/tutorials/mountingpaper.webp" alt="Mounting paper for displaying specimens" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Mounting paper for displaying specimens</h6>
        </div>
    </div>

    <!-- Third Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <img src="img/tutorials/tweezers.jpeg" alt="Tweezers for handling specimens" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Tweezers for handling specimens</h6>
        </div>
        <div class="col-md-4">
            <img src="img/tutorials/plantpress.jpg" alt="Plant press for pressing specimens" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Plant press for pressing specimens</h6>
        </div>
        <div class="col-md-4">
            <img src="img/tutorials/brush.png" alt="Small brush for applying adhesive" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Small brush for applying adhesive</h6>
        </div>
    </div>

    <!-- Fourth Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <img src="img/tutorials/lens.jpg" alt="Hand lens for examination" class="img-fluid tutorial-custom-img rounded shadow">
            <h6 class="tutorial-img-heading">Hand lens for examination</h6>
        </div>
    </div>
</div>

</div>
<?php include_once 'footer.php' ?>
</body>