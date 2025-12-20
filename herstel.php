<?php
/**
 * Herstel Pagina
 *
 * Deze pagina herstelt verwijderde items (punten of bronnen) door de deleted_at kolom op NULL te zetten.
 * Het item wordt weer zichtbaar in het systeem zonder definitief verwijderd te worden.
 */

// Start sessie voor gebruikersbeheer
session_start();

// Controleer of gebruiker is ingelogd
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
    header('Location: inlog.php');
    exit();
}

// Inclusie van database connectie
include 'assets/includes/connectie.php';

// Controleer of vereiste parameters aanwezig zijn
if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type']; // Type item: 'punt' of 'bron'
    $id = intval($_GET['id']); // ID van het item
    $redirect_url = 'verwijdeerd.php'; // Standaard redirect naar verwijderde items pagina

    // Als we panorama_id hebben, ga dan naar bewerk pagina
    if (isset($_GET['panorama_id'])) {
        $panorama_id = intval($_GET['panorama_id']);
        $redirect_url = 'bewerk.php?panorama_id=' . $panorama_id;
    }
    // Als we punt_id hebben voor bron, vind panorama_id
    elseif (isset($_GET['punt_id']) && $type === 'bron') {
        $punt_id = intval($_GET['punt_id']);
        // Haal panorama_id op van het punt waar de bron bij hoort
        $stmt = $conn->prepare("SELECT panorama_id FROM punten WHERE id = ?");
        $stmt->bind_param("i", $punt_id);
        $stmt->execute();
        $stmt->bind_result($panorama_id);
        $stmt->fetch();
        $stmt->close();
        $redirect_url = 'bewerk.php?panorama_id=' . $panorama_id;
    }

    // Kies de juiste SQL query gebaseerd op type
    switch ($type) {
        case 'punt':
            // Herstel punt door deleted_at op NULL te zetten
            $stmt = $conn->prepare("UPDATE punten SET deleted_at = NULL WHERE id = ?");
            break;
        case 'bron':
            // Herstel bron door deleted_at op NULL te zetten
            $stmt = $conn->prepare("UPDATE bronnen SET deleted_at = NULL WHERE id = ?");
            break;
        default:
            // Ongeldig type opgegeven
            $_SESSION['error'] = "Ongeldig type";
            header('Location: ' . $redirect_url);
            exit();
    }

    // Voer de herstel query uit
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Succesvol hersteld
            $_SESSION['success'] = ucfirst($type) . " succesvol hersteld.";
        } else {
            // Fout bij uitvoeren
            $_SESSION['error'] = "Fout bij herstellen.";
        }
        $stmt->close();
    }
}

// Redirect naar de juiste pagina
header('Location: ' . $redirect_url);
exit();
?>