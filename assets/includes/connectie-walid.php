<?php
// Database connection parameters
$servername = "localhost";
$username = "u240653_w_b_devs";
$password = "Cmnx7MJtxZKfwVGRPeaq"; // Default for XAMPP
$dbname = "u240653_w_b_devs";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and execute the SELECT query using prepared statement
$stmt = $conn->prepare("SELECT * FROM panorama");
$stmt->execute();
$result = $stmt->get_result();

$panorama = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $panorama[] = [
            'id' => $row['id'],
            'titel' => $row['titel'],
            'afbeelding_url' => $row['afbeelding_url'],
            'beschrijving' => $row['beschrijving'],
            'gebruiker_id' => $row['gebruiker_id'],
            'status' => $row['status'],
            'aangemaakt_op' => $row['aangemaakt_op'],
            'bijgewerkt_op' => $row['bijgewerkt_op']
        ];
    }
} else {
    echo "0 results";
}


?>