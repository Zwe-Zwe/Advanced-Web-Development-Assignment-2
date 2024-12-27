<?php
session_name('Zwe_Het_Zaw');
session_start();

include_once 'connection.php';

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

// Handle Add Plant Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve form inputs
    $scientific_name = trim($_POST['scientific_name']);
    $common_name = trim($_POST['common_name']);
    $family = trim($_POST['family']);
    $genus = trim($_POST['genus']);
    $species = trim($_POST['species']);
    $herbarium_photo = null;
    $description_file = null;

    // Directories for uploads
    $image_dir = 'img/plants/';
    $description_dir = 'plants_derscription/';

    // File size limits
    $max_photo_size = 5 * 1024 * 1024; // 5 MB in bytes
    $max_pdf_size = 7 * 1024 * 1024;   // 7 MB in bytes

    // Ensure directories exist
    if (!is_dir($image_dir)) mkdir($image_dir, 0777, true);
    if (!is_dir($description_dir)) mkdir($description_dir, 0777, true);

    // Handle Herbarium Photo Upload
    if (isset($_FILES['herbarium_photo']) && $_FILES['herbarium_photo']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['herbarium_photo']['size'] > $max_photo_size) {
            $error = "Herbarium photo must not exceed 5 MB.";
        } else {
            $photo_extension = pathinfo($_FILES['herbarium_photo']['name'], PATHINFO_EXTENSION);
            $herbarium_photo = $scientific_name . '.jpg'; // Save photo as Scientific Name.jpg
            $photo_path = $image_dir . $herbarium_photo;

            if (!move_uploaded_file($_FILES['herbarium_photo']['tmp_name'], $photo_path)) {
                $error = "Failed to upload the herbarium photo.";
            }
        }
    } else {
        $error = "Herbarium photo is required.";
    }

    // Handle Description File Upload
    if (isset($_FILES['description_file']) && $_FILES['description_file']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['description_file']['size'] > $max_pdf_size) {
            $error = "Description file must not exceed 7 MB.";
        } else {
            $file_extension = pathinfo($_FILES['description_file']['name'], PATHINFO_EXTENSION);
            if ($file_extension === 'pdf') {
                $description_file = $scientific_name . '.pdf'; // Save the description as Scientific Name.pdf
                $description_path = $description_dir . $description_file;

                if (!move_uploaded_file($_FILES['description_file']['tmp_name'], $description_path)) {
                    $error = "Failed to upload the description file.";
                }
            } else {
                if (!empty($_FILES['description_file']['name'])) {
                    $error = "Description file must be a PDF.";
                }
            }
        }
    } elseif (empty($_FILES['description_file']['name'])) {
        $description_file = null; // Ensure no file is saved if description file is not provided
    }

    // Validate required fields
    if (empty($scientific_name) || empty($common_name) || empty($family) || empty($genus) || empty($species)) {
        $error = "All fields except the description file are required.";
    }

    // Insert into the database if no errors
    if (!isset($error)) {
        $stmt = $conn->prepare(
            "INSERT INTO plant_table (scientific_name, common_name, family, genus, species, plants_image, description, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')"
        );
        $stmt->bind_param(
            "sssssss",
            $scientific_name,
            $common_name,
            $family,
            $genus,
            $species,
            $herbarium_photo,
            $description_file
        );

        if ($stmt->execute()) {
            $success = "Plant added successfully.";
        } else {
            $error = "Error adding plant: " . $stmt->error;
        }
        $stmt->close();
    }
}


// Handle status update and delete logic
if (isset($_GET['id']) && isset($_GET['action'])) {
    $plant_id = intval($_GET['id']);
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        $new_status = 'approved';
    } elseif ($action === 'reject') {
        $new_status = 'rejected';
    } elseif ($action === 'delete') {
        // Delete plant record
        $stmt = $conn->prepare("DELETE FROM plant_table WHERE id = ?");
        $stmt->bind_param("i", $plant_id);
        $stmt->execute();
        $stmt->close();
    } else {
        $new_status = null;
    }

    if ($new_status) {
        $stmt = $conn->prepare("UPDATE plant_table SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $new_status, $plant_id);
        $stmt->execute();
        $stmt->close();
    }

    // Redirect to avoid re-executing the update on page refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$sql = "SELECT 
            id, 
            scientific_name, 
            common_name, 
            family, 
            genus, 
            SUBSTRING_INDEX(scientific_name, ' ', -1) AS species, 
            status 
        FROM plant_table";
$result = $conn->query($sql);
?>

<?php include_once 'head.php'; ?>
<body>
<?php include_once 'header.php'; ?>
<h1 class="text-center mb-3 plant-table-heading">Plants Management</h1>
<div class="d-flex justify-content-center mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPlantModal">Add Plant</button>
</div>
<div class="container plant-table-container">
    <div class="table-responsive plant-table-responsive">
        <table class="table table-bordered table-sm shadow-sm plant-table">
            <thead>
                <tr>
                    <th class="text-center" style="width: 5%;">ID</th>
                    <th>Scientific Name</th>
                    <th>Common Name</th>
                    <th>Family</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 9%;">Image</th>
                    <th style="width: 20%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $counter = 1; // Row numbers
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td class='text-center'>" . $counter++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['scientific_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['common_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['family']) . "</td>";

                        // Color-coded status with badges
                        $statusBadgeClass = $row['status'] === 'approved' ? 'bg-success text-white'
                            : ($row['status'] === 'rejected' ? 'bg-danger text-white'
                            : 'bg-warning text-dark');
                        echo "<td><span class='badge $statusBadgeClass'>" . ucfirst($row['status']) . "</span></td>";

                        // Image column
                        $imagePath = "img/plants/" . htmlspecialchars($row['scientific_name']) . ".jpg";
                        echo "<td class='text-center'><img src='$imagePath' alt='" . htmlspecialchars($row['scientific_name']) . "' class='img-fluid' style='max-width: 100px;'></td>";

                        // Action buttons
                        echo "<td class='d-flex gap-1 justify-content-center'>";

                        // Buttons for approving, rejecting, or deleting a plant
                        if ($row['status'] === 'pending') {
                            echo "<a href='?id=" . $row['id'] . "&action=approve' class='btn btn-outline-success btn-sm'>Approve</a>";
                            echo "<a href='?id=" . $row['id'] . "&action=reject' class='btn btn-outline-danger btn-sm'>Reject</a>";
                        } elseif ($row['status'] === 'approved') {
                            echo "<button class='btn btn-success btn-sm' disabled>Approved</button>";
                            echo "<a href='?id=" . $row['id'] . "&action=reject' class='btn btn-outline-danger btn-sm'>Reject</a>";
                        } elseif ($row['status'] === 'rejected') {
                            echo "<a href='?id=" . $row['id'] . "&action=approve' class='btn btn-outline-success btn-sm'>Approve</a>";
                            echo "<button class='btn btn-danger btn-sm' disabled>Rejected</button>";
                        }
                        echo "<a href='?id=" . $row['id'] . "&action=delete' class='btn btn-outline-warning btn-sm'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center text-muted py-4'>No plant records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Plant Modal -->
<div class="modal fade" id="addPlantModal" tabindex="-1" aria-labelledby="addPlantModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlantModalLabel">Add New Plant</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="scientific_name" class="form-label">Scientific Name</label>
                        <input type="text" class="form-control" id="scientific_name" name="scientific_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="common_name" class="form-label">Common Name</label>
                        <input type="text" class="form-control" id="common_name" name="common_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="family" class="form-label">Family</label>
                        <input type="text" class="form-control" id="family" name="family" required>
                    </div>
                    <div class="mb-3">
                        <label for="genus" class="form-label">Genus</label>
                        <input type="text" class="form-control" id="genus" name="genus" required>
                    </div>
                    <div class="mb-3">
                        <label for="species" class="form-label">Species</label>
                        <input type="text" class="form-control" id="species" name="species" required>
                    </div>
                    <div class="mb-3">
                        <label for="herbarium_photo" class="form-label">Herbarium Photo</label>
                        <input type="file" class="form-control" id="herbarium_photo" name="herbarium_photo" accept="image/*" required>
                    </div>
                    <div class="mb-3">
                        <label for="description_file" class="form-label">Description PDF (optional)</label>
                        <input type="file" class="form-control" id="description_file" name="description_file" accept="application/pdf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
