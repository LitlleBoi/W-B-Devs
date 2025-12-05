<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // Default for XAMPP
$dbname = "w&b_devs";




// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>