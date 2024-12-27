<?php 
session_name('Zwe_Het_Zaw');
session_start();

// Check if user is logged in, if not, redirect to login page
if (!isset($_SESSION['loggedin'])) {
    header("Location: login.php");
    exit();
}
?>
<?php include_once "head.php" ?>
<body class="classify-body">
    <?php include_once "header.php" ?>
    <div class="container classify-container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Plant Classification Header -->
                <div class="classification-header text-center mb-5">
                    <h2>Plant Classification</h2>
                </div>

                <!-- Card: Understanding Families, Genus, and Species -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title classify-titles">Understanding Families, Genus, and Species</h5>
                        <p class="card-text">In plant taxonomy, plants are classified in hierarchical categories. The three most important categories are Family, Genus, and Species.</p>
                    </div>
                </div>

                <!-- Card: Family -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title classify-titles">Family</h5>
                        <p class="card-text">The <strong>Family</strong> is a broader classification, grouping together plants that share several characteristics, such as flower structure or reproductive features. Families can include multiple genera (plural of genus).</p>
                    </div>
                </div>

                <!-- Card: Genus -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title classify-titles">Genus</h5>
                        <p class="card-text">The <strong>Genus</strong> is a group of species that are closely related and share common traits. The genus name is always capitalized, and it precedes the species name in the binomial nomenclature.</p>
                    </div>
                </div>

                <!-- Card: Species -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title classify-titles">Species</h5>
                        <p class="card-text">The <strong>Species</strong> is the most specific level of classification. It represents individual plants that are capable of interbreeding. The species name follows the genus name and is not capitalized.</p>
                    </div>
                </div>

                <!-- Card: Classification Hierarchy Image -->
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="img/Plants Classification.jpg" alt="Plant Classification Hierarchy" class="img-fluid classify-img mb-3">
                        <p class="card-text">Figure: Hierarchy of Plant Classification from Family to Species</p>
                    </div>
                </div>

                <!-- Card: Example Classification -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title classify-titles">An Example of Plant Classification</h5>
                        <p class="card-text">In this example, we will explore the classification of a plant from the <strong>Dipterocarpaceae</strong> family, focusing on the <strong>Dipterocarpus</strong> genus and the species <em>Dipterocarpus confertus</em>.</p>
                    </div>
                </div>

                <!-- Card: Family -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title classify-titles">Family: Dipterocarpaceae</h5>
                        <p class="card-text">The Dipterocarpaceae family consists of tropical trees that are primarily found in Southeast Asia. These trees are known for their timber and resin.</p>
                    </div>
                    <!-- Example Images for Family -->
                    <div class="row text-center">
                        <div class="col-md-6">
                            <img src="img/plants/family1.jpeg" alt="Monotes kerstingii" class="img-fluid classify-img mb-3">
                            <p class="card-text mb-3">Figure: Monotes kerstingii</p>
                        </div>
                        <div class="col-md-6">
                            <img src="img/plants/family2.jpg" alt="Sal" class="img-fluid classify-img mb-3">
                            <p class="card-text mb-3">Figure: Sal</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Genus -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title classify-titles">Genus: Dipterocarpus</h5>
                        <p class="card-text">The genus <em>Dipterocarpus</em> includes large trees that produce two-winged fruits. These trees are commonly found in rainforests and contribute significantly to forest ecology.</p>
                    </div>
                    <!-- Example Images for Genus -->
                    <div class="row text-center">
                        <div class="col-md-6">
                            <img src="img/plants/genus1.jpeg" alt="Dipterocarpus crinitus" class="img-fluid classify-img mb-3">
                            <p class="card-text mb-3">Figure: Dipterocarpus crinitus</p>
                        </div>
                        <div class="col-md-6">
                            <img src="img/plants/genus2.jpeg" alt="Dipterocarpus palembanicus" class="img-fluid classify-img mb-3">  
                            <p class="card-text mb-3">Figure: Dipterocarpus palembanicus</p>                         
                        </div>
                    </div>
                </div>

                <!-- Card: Species -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title classify-titles">Species: Dipterocarpus confertus</h5>
                        <p class="card-text"><em>Dipterocarpus confertus</em> is a species native to tropical rainforests. It is known for its durable timber and plays a role in maintaining forest canopy structures.</p>
                    </div>
                    <!-- Example Images for Species -->
                    <div class="row text-center">
                        <div class="col-md-6">
                            <img src="img/plants/species1.jpeg" alt="Dipterocarpus confertus" class="img-fluid classify-img mb-3">
                            <p class="card-text mb-3">Figure: Dipterocarpus confertus</p>
                        </div>
                        <div class="col-md-6">
                            <img src="img/plants/species2.jpeg" alt="Dipterocarpus confertus" class="img-fluid classify-img mb-3">
                            <p class="card-text mb-3">Figure: Dipterocarpus confertus</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Additional Resources -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title classify-titles">Additional Resources</h5>
                        <p class="card-text">For more information on plant classification, you can explore the following resources:</p>
                        <ul>
                            <li><a href="https://www.sciencedirect.com/science/article/pii/S0925231221014934" target="_blank">Science Direct: Plant Classification Study</a></li>
                            <li><a href="https://www.americanmeadows.com/content/general-guides/plant-classification?srsltid=AfmBOoovhnywl-QVkIywNQ3jeJ5UmmNwn2mpk_Sdn-yYzJHIiDrHR4Ag" target="_blank">American Meadows: Plant Classification Guide</a></li>
                            <li><a href="https://www.inaturalist.org/taxa/47126-Plantae" target="_blank">iNaturalist</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php include_once "footer.php" ?>
</body>
