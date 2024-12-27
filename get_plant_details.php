<?php
include_once 'connection.php'; // Database connection

// Check if scientific name is provided
if (isset($_GET['scientific_name'])) {
    $scientificName = $_GET['scientific_name'];

    // Prepare the SQL query to fetch plant details from the database
    $sql = "SELECT * FROM plant_table WHERE scientific_name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $scientificName);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a plant is found
    if ($result->num_rows > 0) {
        // Fetch the plant details
        $plant = $result->fetch_assoc();
        
        // Return plant data as JSON
        echo json_encode($plant);
    } else {
        // No plant found
        echo json_encode(['error' => 'Plant details not found']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'No scientific name provided']);
}

$conn->close();
?>
