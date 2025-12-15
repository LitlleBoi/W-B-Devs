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

header('Location: verwijdeerd.php');
exit();
?>