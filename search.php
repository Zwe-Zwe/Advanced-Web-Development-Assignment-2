<?php
session_name('Zwe_Het_Zaw');
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include database connection
include_once 'connection.php';

// Get the search query
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

?>

<?php include_once 'head.php'; ?>

<body>
    <?php include_once 'header.php'; ?>

    <main class="container search-container" id="main-mt">
        <div class="shadow-lg rounded p-4 w-100">
            <h1 class="mb-4">Search Results</h1>
            <?php
            if ($query) {
                // Prepare and execute the search query
                $stmt = $conn->prepare("SELECT scientific_name, common_name, family, genus, description FROM plant_table 
                                        WHERE scientific_name LIKE ? OR common_name LIKE ? OR family LIKE ? OR genus LIKE ?");
                $likeQuery = '%' . $conn->real_escape_string($query) . '%';
                $stmt->bind_param('ssss', $likeQuery, $likeQuery, $likeQuery, $likeQuery);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo '<div class="row">';
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <div class="col-md-4">
                            <div class="card rounded shadow-lg mb-4">
                                <img src="img/plants/' . htmlspecialchars($row['scientific_name']) . '.jpg" class="card-img-top" alt="Plant Image" id="search-img">
                                <div class="card-body">
                                    <h5 class="card-title">' . htmlspecialchars($row['scientific_name']) . '</h5>
                                    <p class="card-text">' . htmlspecialchars($row['common_name']) . '</p>
                                    <a href="plant_detail.php?scientific_name=' . htmlspecialchars($row['scientific_name']) . '" class="btn btn-primary">View Description</a>
                                </div>
                            </div>
                        </div>';
                    }
                    echo '</div>';
                } else {
                    echo '<p>No results found for "' . htmlspecialchars($query) . '".</p>';
                }

                // Close the statement
                $stmt->close();
            } else {
                echo '<p>Please enter a search query.</p>';
            }

            // Close the database connection
            $conn->close();
            ?>
        </div>
    </main>

    <?php include_once 'footer.php'; ?>
</body>

</html>
