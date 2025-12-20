<?php
// Foutmeldingen inschakelen voor debugging
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Inclusie van database selecties
include 'select.php';

// Format data voor JavaScript gebruik
$output = [
    'format' => 'v2',
    'panoramas' => []
];

// Loop door alle panorama's en formatteer de data
foreach ($info as $panorama) {
    $output['panoramas'][] = [
        'titel' => $panorama['titel'],
        'beschrijving' => $panorama['beschrijving'],
        'catalogusnummer' => $panorama['catalogusnummer'] ?? '',
        'auteursrechtlicentie' => $panorama['auteursrechtlicentie'] ?? '',
        'vervaardiger' => $panorama['vervaardiger'] ?? '',
        'jaar' => date('Y', strtotime($panorama['aangemaakt_op'])),
        'locatie' => 'Nederland'
    ];
}

// Stel content type in als JSON
header('Content-Type: application/json');

// Maak buffer schoon voordat JSON wordt verzonden
ob_clean();

// Onderdruk mogelijke warnings/notices in output
error_reporting(0);

// Output JSON data
echo json_encode($output);
?>