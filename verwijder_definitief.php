<?php
/**
 * Definitief Verwijderen Pagina
 *
 * Deze pagina verwerkt het definitief verwijderen van soft-deleted items (punten en bronnen).
 * Alleen ingelogde gebruikers kunnen deze actie uitvoeren. Het verwijderd items uit de database.
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

// Controleer of type en id parameters zijn opgegeven
if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = intval($_GET['id']);

    // Bepaal welke query gebruikt moet worden op basis van type
    switch ($type) {
        case 'punt':
            // Eerst gekoppelde bronnen definitief verwijderen
            $conn->query("DELETE FROM bronnen WHERE punt_id = $id AND deleted_at IS NOT NULL");
            // Dan het punt zelf
            $stmt = $conn->prepare("DELETE FROM punten WHERE id = ? AND deleted_at IS NOT NULL");
            break;
        case 'bron':
            $stmt = $conn->prepare("DELETE FROM bronnen WHERE id = ? AND deleted_at IS NOT NULL");
            break;
        default:
            $_SESSION['error'] = "Ongeldig type";
            header('Location: verwijdeerd.php');
            exit();
    }

    // Voer de verwijder query uit als deze is ingesteld
    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = ucfirst($type) . " definitief verwijderd.";
        } else {
            $_SESSION['error'] = "Fout bij definitief verwijderen.";
        }
        $stmt->close();
    }
}

// Redirect terug naar de verwijderde items pagina
header('Location: verwijdeerd.php');
exit();
?>