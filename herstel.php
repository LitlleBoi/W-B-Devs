<?php

session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
    header('Location: inlog.php');
    exit();
}

include 'assets/includes/connectie.php';

if (isset($_GET['type']) && isset($_GET['id'])) {
    $type = $_GET['type'];
    $id = intval($_GET['id']);
    $redirect_url = 'verwijdeerd.php';

    // Als we panorama_id hebben, ga dan naar bewerk pagina
    if (isset($_GET['panorama_id'])) {
        $panorama_id = intval($_GET['panorama_id']);
        $redirect_url = 'bewerk.php?panorama_id=' . $panorama_id;
    }
    // Als we punt_id hebben voor bron, vind panorama_id
    elseif (isset($_GET['punt_id']) && $type === 'bron') {
        $punt_id = intval($_GET['punt_id']);
        $stmt = $conn->prepare("SELECT panorama_id FROM punten WHERE id = ?");
        $stmt->bind_param("i", $punt_id);
        $stmt->execute();
        $stmt->bind_result($panorama_id);
        $stmt->fetch();
        $stmt->close();
        $redirect_url = 'bewerk.php?panorama_id=' . $panorama_id;
    }

    switch ($type) {
        case 'punt':
            $stmt = $conn->prepare("UPDATE punten SET deleted_at = NULL WHERE id = ?");
            break;
        case 'bron':
            $stmt = $conn->prepare("UPDATE bronnen SET deleted_at = NULL WHERE id = ?");
            break;
        default:
            $_SESSION['error'] = "Ongeldig type";
            header('Location: ' . $redirect_url);
            exit();
    }

    if ($stmt) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = ucfirst($type) . " succesvol hersteld.";
        } else {
            $_SESSION['error'] = "Fout bij herstellen.";
        }
        $stmt->close();
    }
}

header('Location: ' . $redirect_url);
exit();
?>