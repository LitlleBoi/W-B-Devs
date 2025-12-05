<?php
header('Content-Type: application/json');

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "w&b_devs-1";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt_panorama = $conn->prepare("SELECT * FROM panorama");
$stmt_panorama->execute();
$result_panorama = $stmt_panorama->get_result();

$response = [
    'format' => 'v2', // Mark as new format
    'panoramas' => []
];

if ($result_panorama && $result_panorama->num_rows > 0) {
    while ($row = $result_panorama->fetch_assoc()) {
        $response['panoramas'][] = [
            'titel' => $row['titel'],
            'beschrijving' => $row['beschrijving'],
            'catalogusnummer' => $row['catalogusnummer'] ?? 'CT-' . str_pad($row['id'], 3, '0', STR_PAD_LEFT),
            'auteursrechtlicentie' => $row['auteursrechtlicentie'] ?? 'Onbekend',
            'vervaardiger' => $row['vervaardiger'] ?? 'Onbekend',
            'jaar' => $row['jaar'] ?? date('Y')
        ];
    }
}

$stmt_panorama->close();
$conn->close();

echo json_encode($response);
?>