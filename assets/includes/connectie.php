<?php
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "W&BDevs123!"; // Default for XAMPP
$dbname = "w&b_devs";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Connected successfully to the database.<br>";

// Prepare and execute the SELECT query using prepared statement
$stmt = $conn->prepare("SELECT * FROM panorama");
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $panorama[] = [
            'id' => $row['id'],
            'titel' => $row['titel'],
            'afbeelding' => $row['afbeelding'],
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

$stmt->close();
$conn->close();
?>