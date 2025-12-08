<?php
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