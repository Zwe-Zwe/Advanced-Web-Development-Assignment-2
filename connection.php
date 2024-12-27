<?php
$servername = "localhost";
$username = "root";
$password = ""; // replace with your database password

// Create connection without specifying the database initially
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it does not exist
$dbname = "PlantBiodiversity";
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";

// Execute the query to create the database
if ($conn->query($sql) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($dbname);

// Now $conn is connected to the 'PlantBiodiversity' database
?>
