<?php
header('Content-Type: application/json');
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = ""; // Default for XAMPP
$dbname = "w&b_devs-1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$stmt_panorama = $conn->prepare("SELECT * FROM panorama");
$stmt_panorama->execute();
$result_panorama = $stmt_panorama->get_result();

$info = [];
if ($result_panorama && $result_panorama->num_rows > 0) {
    while ($row = $result_panorama->fetch_assoc()) {
        $info[] = $row['titel'];
        $info[] = $row['beschrijving'];
    }
} else {
    // No results, return empty array
    $info = [];
}
$stmt_panorama->close();
$conn->close();

// Output JSON
echo json_encode($info);
?>