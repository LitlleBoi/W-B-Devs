<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// omschrijving-data.php
include 'select.php';

// Format data for JavaScript
$output = [
    'format' => 'v2',
    'panoramas' => []
];

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

header('Content-Type: application/json');
echo json_encode($output);
?>