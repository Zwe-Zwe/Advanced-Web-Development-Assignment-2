<?php
// Start session and handle file upload
include_once 'connection.php';
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
$error = '';
$uploadSuccess = false;
$uploadedImagePath = '';
$plant_id = null;
$plantDetails = null;

// Handle file upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["plantPhoto"])) {
    $targetDir = "img/uploads/";

    if (!file_exists($targetDir)) {
        if (!mkdir($targetDir, 0777, true)) {
            $error = "Failed to create upload directory";
        }
    }

    if (empty($error)) {
        $targetFile = $targetDir . basename($_FILES["plantPhoto"]["name"]);
        $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'jpeg', 'png');

        if (!in_array($imageFileType, $allowedTypes)) {
            $error = "Sorry, only JPG, JPEG & PNG files are allowed.";
        } elseif ($_FILES["plantPhoto"]["error"] !== UPLOAD_ERR_OK) {
            $error = "Upload failed with error code: " . $_FILES["plantPhoto"]["error"];
        } elseif (move_uploaded_file($_FILES["plantPhoto"]["tmp_name"], $targetFile)) {
            $uploadedImagePath = $targetFile;
            $uploadSuccess = true;
        } else {
            $error = "Failed to upload file.";
        }
    }
}
?>

<?php include_once 'head.php'; ?>

<body class="bg-light">
    <?php include_once "header.php"; ?>

    <main id="main-mt mt-5">
        <div class="container identify-container">
            <h1 class="text-center mb-4 mt-5">Plant Identification</h1>

            <div class="row justify-content-center mt-5">
                <div class="col-md-8">
                    <div class="card shadow p-4">
                        <?php if (!empty($error)) { ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php } ?>

                        <?php if ($uploadSuccess) { ?>
                            <div class="identification-results">
                                <div class="row mb-4">
                                    <!-- Uploaded Image -->
                                    <div class="col-md-6">
                                        <h5>Uploaded Image:</h5>
                                        <img src="<?php echo htmlspecialchars($uploadedImagePath); ?>"
                                            class="img-fluid rounded"
                                            alt="Uploaded Plant"
                                            id="uploadedImage">
                                    </div>

                                    <!-- Identification Results -->
                                    <div class="col-md-6">
                                        <h5>Identification Results:</h5>
                                        <div id="predictions" class="mb-3"></div>
                                        <div id="plant-info"></div>
                                    </div>
                                </div>

                                <!-- New Upload Button -->
                                <a href="identify.php" class="btn btn-primary">Upload Another Photo</a>
                            </div>
                        <?php } else { ?>
                            <form action="identify.php" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                                <div class="text-center mb-4">
                                    <label for="plantPhoto" class="form-label fs-5 fw-semibold">
                                        Upload a Plant Photo
                                    </label>
                                    <p class="text-muted small mb-2">
                                        Accepted formats: JPG, JPEG, PNG (Max size: 5MB)
                                    </p>
                                </div>
                                
                                <div class="d-flex flex-column align-items-center mb-4">
                                    <!-- Custom file input -->
                                    <label for="plantPhoto" class="file-upload-wrapper">
                                        <input type="file" 
                                            id="plantPhoto" 
                                            name="plantPhoto" 
                                            class="form-control d-none" 
                                            required 
                                            accept=".jpg,.jpeg,.png" 
                                            onchange="previewImage(this)">
                                        <div class="file-upload-box p-4 border border-2 border-dashed rounded text-center shadow-sm bg-light">
                                            <i class="bi bi-upload fs-1 text-muted"></i>
                                            <p class="mt-2 text-muted">
                                                Drag and drop your file here or click to upload.
                                            </p>
                                        </div>
                                    </label>
                                    <div class="invalid-feedback text-center">
                                        Please select a valid image file.
                                    </div>
                                </div>

                                <!-- Image Preview -->
                                <div class="text-center mb-4" id="imagePreview" style="display: none;">
                                    <img id="preview" src="#" class="img-thumbnail rounded shadow" alt="Preview" style="max-height: 200px;">
                                    <p class="small text-muted mt-2">Selected Image</p>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-success btn-lg w-100 shadow-sm">
                                    <i class="bi bi-search"></i> Identify Plant
                                </button>
                            </form>
                            
                        <?php } ?>
                    </div>
                    <div class="text-center mt-4">
                                <a href="readme.txt" class="btn btn-secondary" download>
                                    <i class="bi bi-file-earmark-text"></i> Download Readme
                                </a>
                            </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@latest/dist/tf.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/image@latest/dist/teachablemachine-image.min.js"></script>
    <script type="text/javascript">
        const URL = "./my_model/";
        let model;

        async function loadModel() {
            const modelURL = URL + "model.json";
            const metadataURL = URL + "metadata.json";
            return await tmImage.load(modelURL, metadataURL);
        }

        async function fetchPlantDetails(scientificName) {
            try {
                const response = await fetch(`get_plant_details.php?scientific_name=${encodeURIComponent(scientificName)}`);
                const data = await response.json();
                return data;
            } catch (error) {
                console.error('Error fetching plant details:', error);
                return null;
            }
        }

        async function predictImage() {
            const img = document.getElementById('uploadedImage');
            const predictionsDiv = document.getElementById('predictions');
            const plantInfoDiv = document.getElementById('plant-info');

            if (!img) return;

            try {
                if (!model) {
                    model = await loadModel();
                }

                const predictions = await model.predict(img);

                // Sort predictions by probability
                const sortedPredictions = predictions.sort((a, b) => b.probability - a.probability);

                // Display predictions
                let predictionsHTML = '<div class="list-group mb-3">';
                let bestMatch = null;

                for (const prediction of sortedPredictions) {
                    if (prediction.probability > 0.15) { // Only show predictions with >15% confidence
                        const confidence = (prediction.probability * 100).toFixed(1);
                        const confidenceClass = confidence > 70 ? 'text-success' : 'text-muted';

                        predictionsHTML += `
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>${prediction.className}</span>
                                    <span class="${confidenceClass}">${confidence}%</span>
                                </div>
                            </div>
                        `;

                        // Store best match if confidence is high enough
                        if (prediction === sortedPredictions[0] && prediction.probability > 0.70) {
                            bestMatch = prediction.className;
                        }
                    }
                }
                predictionsHTML += '</div>';
                predictionsDiv.innerHTML = predictionsHTML;

                if (bestMatch) {
                    // Fetch plant details from the database based on the scientific name predicted
                    const plantDetails = await fetchPlantDetails(bestMatch);

                    if (plantDetails && !plantDetails.error) {
                        // The description column holds the PDF filename
                        const pdfFileName = plantDetails.description; // PDF filename is in description
                        const pdfFilePath = `plants_description/${pdfFileName}`;

                        // Check if the PDF file exists in the plants_description folder
                        const pdfExists = await fileExists(pdfFilePath);

                        // Display plant details and the PDF download button if available
                        plantInfoDiv.innerHTML = `
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Plant Details:</h5>
                                    <table class="table table-borderless">
                                        <tr>
                                            <th>Scientific Name:</th>
                                            <td><em>${plantDetails.scientific_name}</em></td>
                                        </tr>
                                        <tr>
                                            <th>Common Name:</th>
                                            <td>${plantDetails.common_name}</td>
                                        </tr>
                                        <tr>
                                            <th>Family:</th>
                                            <td>${plantDetails.family || 'Not available'}</td>
                                        </tr>
                                        <tr>
                                            <th>Genus:</th>
                                            <td>${plantDetails.genus || 'Not available'}</td>
                                        </tr>
                                        <tr>
                                            <th>Species:</th>
                                            <td>${plantDetails.species || 'Not available'}</td>
                                        </tr>
                                    </table>

                                    ${pdfExists ? `
                                        <a href="${pdfFilePath}" class="btn btn-info" download>Download PDF</a>
                                    ` : `
                                        <p class="text-warning">No PDF is uploaded for this plant.</p>
                                        <a href="download.php?scientific_name=${encodeURIComponent(plantDetails.scientific_name)}" class="btn btn-warning">Download Generated PDF</a>
                                    `}
                                </div>
                            </div>
                        `;
                    } else {
                        plantInfoDiv.innerHTML = `
                            <div class="alert alert-warning">
                                ${plantDetails.error || 'Plant details not found in database.'}
                            </div>
                        `;
                    }
                } else {
                    plantInfoDiv.innerHTML = `
                        <div class="alert alert-info">
                            Confidence level too low for a definitive match. Please try uploading another image.
                        </div>
                    `;
                }
            } catch (error) {
                console.error('Error during prediction:', error);
                predictionsDiv.innerHTML = `
                    <div class="alert alert-danger">
                        Error during plant identification. Please try again.
                    </div>
                `;
            }
        }

        // Function to check if file exists
        async function fileExists(filePath) {
            try {
                const response = await fetch(filePath, { method: 'HEAD' });
                return response.ok;
            } catch (error) {
                console.error('Error checking file:', error);
                return false;
            }
        }

        // Initialize prediction when an image is uploaded
        if (document.getElementById('uploadedImage')) {
            predictImage();
        }

        // Preview image before uploading
        function previewImage(input) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                const preview = document.getElementById('preview');
                const imagePreview = document.getElementById('imagePreview');
                preview.src = e.target.result;
                imagePreview.style.display = 'block';
            };

            reader.readAsDataURL(file);
        }
    </script>

<?php include "footer.php"; ?>    
</body>
</html>
