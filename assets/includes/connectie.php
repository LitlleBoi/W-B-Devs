<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // Default for XAMPP
$dbname = "w_b_devs_new";




// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->close();
?>