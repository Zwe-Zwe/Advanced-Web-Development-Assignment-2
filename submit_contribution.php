<?php
// Include the database connection file
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $scientific_name = trim($_POST['scientific_name'] ?? '');
    $common_name = trim($_POST['common_name'] ?? '');
    $family = trim($_POST['family'] ?? '');
    $genus = trim($_POST['genus'] ?? '');
    $species = trim($_POST['species'] ?? '');

    // Validate required fields
    if (empty($scientific_name) || empty($common_name) || empty($family) || empty($genus) || empty($species)) {
        echo "Error: All fields except the description file must be filled out.";
        exit();
    }

    // File size limits
    $max_image_size = 5 * 1024 * 1024; // 5 MB in bytes
    $max_pdf_size = 7 * 1024 * 1024;   // 7 MB in bytes

    // Directories for uploads
    $image_dir = 'img/plants/';
    $description_dir = 'plants_description/';
    $image_path = null;
    $description_path = null;

    // Ensure directories exist
    if (!is_dir($image_dir)) {
        mkdir($image_dir, 0777, true);
    }
    if (!is_dir($description_dir)) {
        mkdir($description_dir, 0777, true);
    }

    // Handle herbarium photo upload
    if (!isset($_FILES['herbarium_photo']) || $_FILES['herbarium_photo']['error'] !== UPLOAD_ERR_OK) {
        echo "Error: Herbarium photo is required.";
        exit();
    }

    if ($_FILES['herbarium_photo']['size'] > $max_image_size) {
        echo "Error: Herbarium photo must not exceed 5 MB.";
        exit();
    }

    $image_name = $scientific_name . '.jpg';
    $image_path = $image_dir . basename($image_name);

    // Move uploaded file to destination
    if (!move_uploaded_file($_FILES['herbarium_photo']['tmp_name'], $image_path)) {
        echo "Error uploading the herbarium photo.";
        exit();
    }

    // Handle description PDF upload
    $pdf_name = null; // Default value if no PDF is uploaded
    if (isset($_FILES['description_file']) && $_FILES['description_file']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['description_file']['size'] > $max_pdf_size) {
            echo "Error: Description file must not exceed 7 MB.";
            exit();
        }

        $pdf_name = $scientific_name . '.pdf'; // Unique PDF name
        $description_path = $description_dir . basename($pdf_name);

        // Move uploaded file to destination
        if (!move_uploaded_file($_FILES['description_file']['tmp_name'], $description_path)) {
            echo "Error uploading the description file.";
            exit();
        }
    }

    // Insert into the database
    $stmt = $conn->prepare("INSERT INTO plant_table (scientific_name, common_name, family, genus, species, plants_image, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sssssss", $scientific_name, $common_name, $family, $genus, $species, $image_name, $pdf_name);

    if ($stmt->execute()) {
        // Redirect on success
        header("Location: contribute.php?success=1");
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close statement
    $stmt->close();
}

// Close connection
$conn->close();
?>
