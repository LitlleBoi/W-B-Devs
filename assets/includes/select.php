<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Detect environment
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || preg_match('/^192\.168\.2/', $_SERVER['SERVER_ADDR'])) {
    // Offline/local omgeving
    $servername = "localhost";
    $username = "root";
    $password = ""; // Default for XAMPP
    $dbname = "w_b_devs_new";
} else {
    // Online/production omgeving
    $servername = "localhost";
    $username = "u240653_w_b_devs";
    $password = "fTzkCnbeDBnsj7xwrL5g"; // Default for XAMPP
    $dbname = "u240653_w_b_devs";
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// echo "Connected successfully to the database.<br>";

//  Haal panorama gegevens op
$stmt_panorama = $conn->prepare("SELECT * FROM panorama");
$stmt_panorama->execute();
$result_panorama = $stmt_panorama->get_result();

$info = [];
if ($result_panorama && $result_panorama->num_rows > 0) {
    while ($row = $result_panorama->fetch_assoc()) {
        $info[] = [
            'id' => $row['id'],
            'titel' => $row['titel'],
            'afbeelding' => $row['afbeelding_url'] ?? '',
            'beschrijving' => $row['beschrijving'],
            'catalogusnummer' => $row['catalogusnummer'] ?? '',
            'auteursrechtlicentie' => $row['auteursrechtlicentie'] ?? '',
            'vervaardiger' => $row['vervaardiger'] ?? '',
            'gebruiker_id' => $row['gebruiker_id'],
            'pagina' => $row['pagina'],
            'status' => $row['status'],
            'aangemaakt_op' => $row['aangemaakt_op'],
            'bijgewerkt_op' => $row['bijgewerkt_op']
        ];
    }
} else {
    // No panoramas found
}


//  Haal punten op  
$stmt_punten = $conn->prepare("SELECT * FROM punten WHERE status = 'gepubliceerd' AND deleted_at IS NULL");
$stmt_punten->execute();
$result_punten = $stmt_punten->get_result();

$punten = [];
if ($result_punten && $result_punten->num_rows > 0) {
    while ($row = $result_punten->fetch_assoc()) {
        $punten[] = [
            'id' => $row['id'],
            'titel' => $row['titel'],
            'x' => $row['x_coordinaat'] ?? '',
            'y' => $row['y_coordinaat'] ?? '',
            'hoogte' => $row['hoogte'] ?? '',
            'breedte' => $row['breedte'] ?? '',
            'omschrijving' => $row['omschrijving'],
            'panorama_id' => $row['panorama_id'] ?? '',
            'gebruiker_id' => $row['gebruiker_id'],
            'status' => $row['status'],
            'aangemaakt_op' => $row['aangemaakt_op'],
            'bijgewerkt_op' => $row['bijgewerkt_op']
        ];
    }
} else {
    // No punten found
}

//  Haal bronnen op
$stmt_bronnen = $conn->prepare("SELECT * FROM bronnen WHERE status = 'gepubliceerd' AND deleted_at IS NULL");
$stmt_bronnen->execute();
$result_bronnen = $stmt_bronnen->get_result();

$bronnen = [];
if ($result_bronnen && $result_bronnen->num_rows > 0) {
    while ($row = $result_bronnen->fetch_assoc()) {
        $bronnen[] = [
            'id' => $row['id'],
            'punt_id' => $row['punt_id'],
            'bron_type' => $row['bron_type'],
            'titel' => $row['titel'],
            'auteur' => $row['auteur'],
            'catalogusnummer' => $row['catalogusnummer'],
            'publicatie_jaar' => $row['publicatie_jaar'],
            'afbeelding' => $row['bron-afbeelding'],
            'status' => $row['status'],
            'referentie_tekst' => $row['referentie_tekst']
        ];
    }
} else {
    // No bronnen found
}

//  SLUIT ALLE STATEMENTS EN CONNECTIE
$stmt_panorama->close();
$stmt_punten->close();
$stmt_bronnen->close();
$conn->close();
?>