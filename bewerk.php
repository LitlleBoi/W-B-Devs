<?php
session_start();

// Simple access control
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
    header('Location: inlog.php');
    exit();
}

include 'assets/includes/connectie.php';

if (!$conn) {
    die("Database connection failed");
}

// EERST panorama_id krijgen - corrigeer de parameter naam
$panorama_id = 0;

if (isset($_GET['panorama_id']) && (int) $_GET['panorama_id'] > 0) {
    $panorama_id = (int) $_GET['panorama_id'];
} elseif (isset($_GET['id']) && (int) $_GET['id'] > 0) {
    $panorama_id = (int) $_GET['id'];
} elseif (isset($_POST['id']) && (int) $_POST['id'] > 0) {
    $panorama_id = (int) $_POST['id'];
}

if ($panorama_id <= 0) {
    echo "Ongeldig panorama ID";
    exit;
}

$gebruiker_id = isset($_SESSION['gebruiker_id']) ? $_SESSION['gebruiker_id'] : 1;

// Delete actions - gebruik item_id voor punt/bron
if (isset($_GET['delete_item_id']) && isset($_GET['type'])) {
    $delete_id = intval($_GET['delete_item_id']);
    $type = $_GET['type'];

    if ($type === 'punt') {
        $stmt = $conn->prepare("UPDATE punten SET deleted_at = NOW() WHERE id = ? AND panorama_id = ?");
        $stmt->bind_param("ii", $delete_id, $panorama_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Punt succesvol verwijderd.";
        }
        $stmt->close();
    } elseif ($type === 'bron') {
        // Eerst de bestandspad ophalen om de foto te verwijderen
        $stmt_select = $conn->prepare("SELECT `bron-afbeelding` FROM bronnen WHERE id = ?");
        $stmt_select->bind_param("i", $delete_id);
        $stmt_select->execute();
        $stmt_select->bind_result($file_path);
        $stmt_select->fetch();
        $stmt_select->close();
        
        // Bestand verwijderen als het lokaal is opgeslagen
        if (!empty($file_path) && strpos($file_path, 'assets/images/aanvullend/') !== false) {
            deleteFile($file_path);
        }
        
        // Bron markeren als verwijderd
        $stmt = $conn->prepare("UPDATE bronnen SET deleted_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Bron succesvol verwijderd.";
        }
        $stmt->close();
    }

    header("Location: bewerk.php?panorama_id=" . $panorama_id);
    exit();
}

// Status change via GET - gebruik item_id
if (isset($_GET['change_status']) && isset($_GET['item_id']) && isset($_GET['type']) && isset($_GET['status'])) {
    $item_id = intval($_GET['item_id']);
    $type = $_GET['type'];
    $status = $_GET['status'];

    if (in_array($status, ['concept', 'gepubliceerd', 'gearchiveerd'])) {
        if ($type === 'punt') {
            $stmt = $conn->prepare("UPDATE punten SET status = ? WHERE id = ? AND panorama_id = ?");
            $stmt->bind_param("sii", $status, $item_id, $panorama_id);
        } elseif ($type === 'bron') {
            $stmt = $conn->prepare("UPDATE bronnen SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $item_id);
        }

        if ($stmt) {
            $stmt->execute();
            $stmt->close();
            $_SESSION['success'] = "Status succesvol gewijzigd naar " . $status . ".";

            // DIRECT DE DATA OPNIEUW OPHALEN NA UPDATE
            // Dit zorgt dat de pagina de laatste status toont
            header("Location: bewerk.php?panorama_id=" . $panorama_id);
            exit();
        }
    }
}

// Get panorama data
$stmt_panorama = $conn->prepare("SELECT * FROM panorama WHERE id = ?");
if (!$stmt_panorama) {
    die("Prepare failed: " . $conn->error);
}
$stmt_panorama->bind_param("i", $panorama_id);
$stmt_panorama->execute();
$result_panorama = $stmt_panorama->get_result();

if (!$result_panorama) {
    die("Query failed: " . $conn->error);
}

$panorama_row = $result_panorama->fetch_assoc();

if (!$panorama_row) {
    echo "Panorama niet gevonden in database voor ID: $panorama_id";
    exit;
}

// Get points - FIX: Zorg dat status altijd een waarde heeft
$punten = [];
$stmt_punten = $conn->prepare("SELECT id, x_coordinaat, y_coordinaat, panorama_id, titel, omschrijving, COALESCE(status, 'concept') as status FROM punten WHERE panorama_id = ? AND deleted_at IS NULL");
if ($stmt_punten) {
    $stmt_punten->bind_param("i", $panorama_id);
    $stmt_punten->execute();
    $result_punten = $stmt_punten->get_result();

    if ($result_punten && $result_punten->num_rows > 0) {
        while ($punt_row = $result_punten->fetch_assoc()) {
            $punt_id = $punt_row['id'];

            // Get sources for this point
            $bronnen = [];
            $stmt_bronnen = $conn->prepare("SELECT * FROM bronnen WHERE punt_id = ? AND deleted_at IS NULL");
            if ($stmt_bronnen) {
                $stmt_bronnen->bind_param("i", $punt_id);
                $stmt_bronnen->execute();
                $result_bronnen = $stmt_bronnen->get_result();

                if ($result_bronnen && $result_bronnen->num_rows > 0) {
                    while ($bron_row = $result_bronnen->fetch_assoc()) {
                        $bronnen[] = [
                            'id' => $bron_row['id'],
                            'referentie_tekst' => $bron_row['referentie_tekst'] ?? '',
                            'titel' => $bron_row['titel'] ?? '',
                            'auteur' => $bron_row['auteur'] ?? '',
                            'bron-afbeelding' => $bron_row['bron-afbeelding'] ?? '',
                            'bron_type' => $bron_row['bron_type'] ?? 'website',
                            'publicatie_jaar' => $bron_row['publicatie_jaar'] ?? '',
                            'catalogusnummer' => $bron_row['catalogusnummer'] ?? '',
                            'status' => $bron_row['status'] ?? 'concept'
                        ];
                    }
                }
            }

            $punten[] = [
                'id' => $punt_id,
                'x' => $punt_row['x_coordinaat'],
                'y' => $punt_row['y_coordinaat'],
                'panorama_id' => $punt_row['panorama_id'],
                'titel' => $punt_row['titel'] ?? '',
                'omschrijving' => $punt_row['omschrijving'] ?? '',
                'status' => $punt_row['status'] ?? 'concept',
                'bronnen' => $bronnen
            ];
        }
    }
}

// Messages
if (isset($_SESSION['success'])) {
    $success_msg = $_SESSION['success'];
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    $error_msg = $_SESSION['error'];
    unset($_SESSION['error']);
}

// File upload function
function uploadFile($fileData, $target_dir = "assets/images/aanvullend/")
{
    if (!isset($fileData['error']) || $fileData['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($fileData['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = basename($fileData['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    $unique_name = uniqid() . '_' . time() . '.' . $file_ext;
    $target_file = $target_dir . $unique_name;

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($file_ext, $allowed_types)) {
        return null;
    }

    if ($fileData['size'] > 5000000) {
        return null;
    }

    if (move_uploaded_file($fileData['tmp_name'], $target_file)) {
        return $target_file;
    }

    return null;
}
// File delete function
function deleteFile($file_path) {
    if (!empty($file_path) && file_exists($file_path) && strpos($file_path, 'assets/images/aanvullend/') !== false) {
        unlink($file_path);
        return true;
    }
    return false;
}
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Update panorama basics
    $titel = $_POST['titel'] ?? '';
    $beschrijving = $_POST['beschrijving'] ?? '';
    $catalogusnummer = $_POST['catalogusnummer'] ?? '';
    $afbeelding = $_POST['afbeelding_url'] ?? '';

    $stmt_update_panorama = $conn->prepare("UPDATE panorama SET titel = ?, beschrijving = ?, catalogusnummer = ?, afbeelding_url = ? WHERE id = ?");
    if ($stmt_update_panorama) {
        $stmt_update_panorama->bind_param("ssssi", $titel, $beschrijving, $catalogusnummer, $afbeelding, $panorama_id);
        $stmt_update_panorama->execute();
        $stmt_update_panorama->close(); // <-- Sluit de statement hier
    }

    // Update existing points - FIXED VERSION
    if (isset($_POST['punten']) && is_array($_POST['punten'])) {
        foreach ($_POST['punten'] as $punt_id => $punt_data) {
            $x = $punt_data['x'] ?? 0;
            $y = $punt_data['y'] ?? 0;
            $punt_titel = $punt_data['titel'] ?? '';
            $punt_omschrijving = $punt_data['omschrijving'] ?? '';
            $punt_status = $punt_data['status'] ?? 'concept';

            // Validatie: zorg dat status altijd geldig is
            if (!in_array($punt_status, ['concept', 'gepubliceerd', 'gearchiveerd'])) {
                $punt_status = 'concept';
            }

            $stmt_update_punt = $conn->prepare("UPDATE punten SET x_coordinaat = ?, y_coordinaat = ?, titel = ?, omschrijving = ?, status = ? WHERE id = ? AND panorama_id = ?");
            if ($stmt_update_punt) {
                $stmt_update_punt->bind_param("iissiii", $x, $y, $punt_titel, $punt_omschrijving, $punt_status, $punt_id, $panorama_id);
                $stmt_update_punt->execute();
                $stmt_update_punt->close();
            }
        }
    }

    // Update existing sources
    if (isset($_POST['bronnen']) && is_array($_POST['bronnen'])) {
        foreach ($_POST['bronnen'] as $bron_id => $bron_data) {
            $referentie_tekst = $bron_data['referentie_tekst'] ?? '';
            $titel = $bron_data['titel'] ?? '';
            $auteur = $bron_data['auteur'] ?? '';
            $bron_afbeelding = $bron_data['bron-afbeelding'] ?? '';
            $bron_type = $bron_data['bron_type'] ?? 'website';
            $publicatie_jaar = $bron_data['publicatie_jaar'] ?? '';
            $catalogusnummer = $bron_data['catalogusnummer'] ?? '';
            $status = $bron_data['status'] ?? 'concept';

            // Check for uploaded file
            $file_input_name = "bron_afbeelding_" . $bron_id;
            if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] === UPLOAD_ERR_OK) {
                // Eerst het huidige bestandspad ophalen om te verwijderen
                $stmt_select = $conn->prepare("SELECT `bron-afbeelding` FROM bronnen WHERE id = ?");
                $stmt_select->bind_param("i", $bron_id);
                $stmt_select->execute();
                $stmt_select->bind_result($old_file_path);
                $stmt_select->fetch();
                $stmt_select->close();
                
                // Oude bestand verwijderen als het lokaal is opgeslagen
                if (!empty($old_file_path) && strpos($old_file_path, 'assets/images/aanvullend/') !== false) {
                    deleteFile($old_file_path);
                }
                
                // Nieuw bestand uploaden
                $uploaded_file = uploadFile($_FILES[$file_input_name]);
                if ($uploaded_file) {
                    $bron_afbeelding = $uploaded_file;
                }
            }

            $stmt_update_bron = $conn->prepare("UPDATE bronnen SET referentie_tekst = ?, titel = ?, auteur = ?, `bron-afbeelding` = ?, bron_type = ?, publicatie_jaar = ?, catalogusnummer = ?, status = ? WHERE id = ?");
            if ($stmt_update_bron) {
                $stmt_update_bron->bind_param("ssssssssi", $referentie_tekst, $titel, $auteur, $bron_afbeelding, $bron_type, $publicatie_jaar, $catalogusnummer, $status, $bron_id);
                $stmt_update_bron->execute();
                $stmt_update_bron->close();
            }
        }
    }

    // Add new points
    if (isset($_POST['new_punten']) && is_array($_POST['new_punten'])) {
        foreach ($_POST['new_punten'] as $temp_id => $punt_data) {
            $x = $punt_data['x'] ?? 0;
            $y = $punt_data['y'] ?? 0;
            $punt_titel = $punt_data['titel'] ?? '';
            $punt_omschrijving = $punt_data['omschrijving'] ?? '';
            $punt_status = $punt_data['status'] ?? 'concept';

            $stmt_insert_punt = $conn->prepare("INSERT INTO punten (panorama_id, x_coordinaat, y_coordinaat, titel, omschrijving, status, gebruiker_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            if ($stmt_insert_punt) {
                $stmt_insert_punt->bind_param("iiisssi", $panorama_id, $x, $y, $punt_titel, $punt_omschrijving, $punt_status, $gebruiker_id);
                $stmt_insert_punt->execute();
                $new_punt_id = $stmt_insert_punt->insert_id;

                // Add sources for this new point
                if (isset($_POST['new_bronnen'][$temp_id]) && is_array($_POST['new_bronnen'][$temp_id])) {
                    foreach ($_POST['new_bronnen'][$temp_id] as $bron_temp_id => $bron_data) {
                        $referentie_tekst = $bron_data['referentie_tekst'] ?? '';
                        $bron_titel = $bron_data['titel'] ?? '';
                        $auteur = $bron_data['auteur'] ?? '';
                        $bron_afbeelding = $bron_data['bron-afbeelding'] ?? '';
                        $bron_type = $bron_data['bron_type'] ?? 'website';
                        $publicatie_jaar = $bron_data['publicatie_jaar'] ?? '';
                        $catalogusnummer = $bron_data['catalogusnummer'] ?? '';
                        $status = $bron_data['status'] ?? 'concept';

                        // Handle file upload for this source
                        if (isset($_FILES['new_bron_afbeelding'][$temp_id][$bron_temp_id]) &&
                            $_FILES['new_bron_afbeelding'][$temp_id][$bron_temp_id]['error'] === UPLOAD_ERR_OK) {
                            $uploaded_file = uploadFile($_FILES['new_bron_afbeelding'][$temp_id][$bron_temp_id]);
                            if ($uploaded_file) {
                                $bron_afbeelding = $uploaded_file;
                            }
                        }

                        $stmt_insert_bron = $conn->prepare("INSERT INTO bronnen (punt_id, titel, auteur, referentie_tekst, `bron-afbeelding`, bron_type, publicatie_jaar, catalogusnummer, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        if ($stmt_insert_bron) {
                            $stmt_insert_bron->bind_param("issssssss", $new_punt_id, $bron_titel, $auteur, $referentie_tekst, $bron_afbeelding, $bron_type, $publicatie_jaar, $catalogusnummer, $status);
                            $stmt_insert_bron->execute();
                            $stmt_insert_bron->close();
                        }
                    }
                }
                $stmt_insert_punt->close(); // <-- Sluit de insert_punt statement hier
            }
        }
    }

    // Add new sources to existing points
    if (isset($_POST['new_bronnen_to_existing']) && is_array($_POST['new_bronnen_to_existing'])) {
        foreach ($_POST['new_bronnen_to_existing'] as $punt_id => $bronnen_data) {
            if (is_array($bronnen_data)) {
                foreach ($bronnen_data as $bron_temp_id => $bron_data) {
                    $referentie_tekst = $bron_data['referentie_tekst'] ?? '';
                    $bron_titel = $bron_data['titel'] ?? '';
                    $auteur = $bron_data['auteur'] ?? '';
                    $bron_afbeelding = $bron_data['bron-afbeelding'] ?? '';
                    $bron_type = $bron_data['bron_type'] ?? 'website';
                    $publicatie_jaar = $bron_data['publicatie_jaar'] ?? '';
                    $catalogusnummer = $bron_data['catalogusnummer'] ?? '';
                    $status = $bron_data['status'] ?? 'concept';

                    // Handle file upload
                    if (isset($_FILES['new_existing_bron_afbeelding'][$punt_id][$bron_temp_id]) &&
                        $_FILES['new_existing_bron_afbeelding'][$punt_id][$bron_temp_id]['error'] === UPLOAD_ERR_OK) {
                        $uploaded_file = uploadFile($_FILES['new_existing_bron_afbeelding'][$punt_id][$bron_temp_id]);
                        if ($uploaded_file) {
                            $bron_afbeelding = $uploaded_file;
                        }
                    }

                    $stmt_insert_bron = $conn->prepare("INSERT INTO bronnen (punt_id, titel, auteur, referentie_tekst, `bron-afbeelding`, bron_type, publicatie_jaar, catalogusnummer, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    if ($stmt_insert_bron) {
                        $stmt_insert_bron->bind_param("issssssss", $punt_id, $bron_titel, $auteur, $referentie_tekst, $bron_afbeelding, $bron_type, $publicatie_jaar, $catalogusnummer, $status);
                        $stmt_insert_bron->execute();
                        $stmt_insert_bron->close();
                    }
                }
            }
        }
    }

    $_SESSION['success'] = "Alle wijzigingen zijn opgeslagen!";
    header("Location: bewerk.php?panorama_id=" . $panorama_id);
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bewerk Panorama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bewerk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="edit-page">
    <?php include 'assets/includes/header.php'; ?>

    <div class="edit-form-container">
        <!-- Messages -->
        <?php if (isset($success_msg)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_msg); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_msg)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_msg); ?>
            </div>
        <?php endif; ?>

        <div class="form-header">
            <h2>Bewerk Panorama: <?php echo htmlspecialchars($panorama_row['titel']); ?></h2>
        </div>

        <!-- Templates -->
        <div class="templates" style="display: none;">
            <!-- Template for new point -->
            <div id="newPointTemplate">
                <div class="new-point-card" data-temp-id="">
                    <div class="action-header">
                        <h4>Nieuw Punt</h4>
                        <div class="point-actions">
                            <select class="status-select new-punt-status" name="new_punten[TEMP_ID][status]">
                                <option value="concept">Concept</option>
                                <option value="gepubliceerd" selected>Gepubliceerd</option>
                                <option value="gearchiveerd">Gearchiveerd</option>
                            </select>
                            <button type="button" class="delete-btn remove-new-point">× Verwijder</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Titel:</label>
                                <input type="text" class="form-control new-punt-titel" name="new_punten[TEMP_ID][titel]"
                                    placeholder="Punt titel" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Omschrijving:</label>
                        <textarea class="form-control new-punt-omschrijving" name="new_punten[TEMP_ID][omschrijving]"
                            rows="3" placeholder="Punt omschrijving"></textarea>
                    </div>

                    <div class="coord-inputs">
                        <div class="input-group">
                            <label class="form-label">X-coördinaat:</label>
                            <input type="number" class="form-control coord-x-input new-punt-x"
                                name="new_punten[TEMP_ID][x]" value="100" required>
                        </div>
                        <div class="input-group">
                            <label class="form-label">Y-coördinaat:</label>
                            <input type="number" class="form-control coord-y-input new-punt-y"
                                name="new_punten[TEMP_ID][y]" value="100" required>
                        </div>
                    </div>

                    <!-- Sources for this point -->
                    <div class="new-bronnen-for-point" style="margin-top: 20px;">
                        <button type="button" class="btn btn-success add-new-bron-to-point"
                            style="margin-bottom: 15px;">
                            + Bron Toevoegen aan dit Punt
                        </button>
                        <div class="point-bronnen-container">
                            <!-- New sources will be added here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template for new source (for NEW points) -->
            <div id="newBronTemplate">
                <div class="new-source-card" data-temp-id="">
                    <div class="action-header">
                        <h5>Nieuwe Bron</h5>
                        <div class="point-actions">
                            <select class="status-select new-bron-status" name="new_bronnen[POINT_ID][TEMP_ID][status]">
                                <option value="concept">Concept</option>
                                <option value="gepubliceerd" selected>Gepubliceerd</option>
                                <option value="gearchiveerd">Gearchiveerd</option>
                            </select>
                            <button type="button" class="delete-btn remove-new-bron">× Verwijder</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Titel:</label>
                                <input type="text" class="form-control new-bron-titel"
                                    name="new_bronnen[POINT_ID][TEMP_ID][titel]" placeholder="Bron titel">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Auteur:</label>
                                <input type="text" class="form-control new-bron-auteur"
                                    name="new_bronnen[POINT_ID][TEMP_ID][auteur]" placeholder="Auteur">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Type:</label>
                                <select class="form-control new-bron-type"
                                    name="new_bronnen[POINT_ID][TEMP_ID][bron_type]">
                                    <option value="boek">Boek</option>
                                    <option value="artikel">Artikel</option>
                                    <option value="website" selected>Website</option>
                                    <option value="video">Video</option>
                                    <option value="document">Document</option>
                                    <option value="ander">Ander</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Publicatiejaar:</label>
                                <input type="text" class="form-control new-bron-jaar"
                                    name="new_bronnen[POINT_ID][TEMP_ID][publicatie_jaar]" placeholder="2023">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Catalogusnummer:</label>
                                <input type="text" class="form-control new-bron-catalogus"
                                    name="new_bronnen[POINT_ID][TEMP_ID][catalogusnummer]" placeholder="12345">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Referentie tekst:</label>
                        <textarea class="form-control new-bron-referentie"
                            name="new_bronnen[POINT_ID][TEMP_ID][referentie_tekst]" rows="2"
                            placeholder="Referentie tekst"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bron Afbeelding:</label>
                        <div class="file-upload-container" style="margin-bottom: 10px;">
                            <input type="file" class="file-upload-input new-bron-file"
                                name="new_bron_afbeelding[POINT_ID][TEMP_ID]" accept="image/*">
                            <button type="button" class="btn btn-secondary upload-btn">
                                <i class="fas fa-upload"></i> Kies afbeelding
                            </button>
                            <span class="file-name">Geen bestand gekozen</span>
                        </div>
                        <input type="text" class="form-control" style="margin-top: 10px;"
                            placeholder="of voer een URL in" name="new_bronnen[POINT_ID][TEMP_ID][bron-afbeelding]">
                        <div class="bron-image-preview new-bron-preview" style="margin-top: 10px;">
                            <p class="text-muted" style="font-size: 12px;">
                                Geen afbeelding geselecteerd
                            </p>
                        </div>
                        <small class="text-muted">Upload een bestand of voer een URL in</small>
                    </div>
                </div>
            </div>

            <!-- Template for new source (for EXISTING points) -->
            <div id="newBronToExistingTemplate">
                <div class="new-source-card" data-temp-id="">
                    <div class="action-header">
                        <h5>Nieuwe Bron</h5>
                        <div class="point-actions">
                            <select class="status-select" name="new_bronnen_to_existing[POINT_ID][TEMP_ID][status]">
                                <option value="concept">Concept</option>
                                <option value="gepubliceerd" selected>Gepubliceerd</option>
                                <option value="gearchiveerd">Gearchiveerd</option>
                            </select>
                            <button type="button" class="delete-btn remove-existing-new-bron">× Verwijder</button>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Titel:</label>
                                <input type="text" class="form-control"
                                    name="new_bronnen_to_existing[POINT_ID][TEMP_ID][titel]" placeholder="Bron titel">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Auteur:</label>
                                <input type="text" class="form-control"
                                    name="new_bronnen_to_existing[POINT_ID][TEMP_ID][auteur]" placeholder="Auteur">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Type:</label>
                                <select class="form-control"
                                    name="new_bronnen_to_existing[POINT_ID][TEMP_ID][bron_type]">
                                    <option value="boek">Boek</option>
                                    <option value="artikel">Artikel</option>
                                    <option value="website" selected>Website</option>
                                    <option value="video">Video</option>
                                    <option value="document">Document</option>
                                    <option value="ander">Ander</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Publicatiejaar:</label>
                                <input type="text" class="form-control"
                                    name="new_bronnen_to_existing[POINT_ID][TEMP_ID][publicatie_jaar]"
                                    placeholder="2023">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Catalogusnummer:</label>
                                <input type="text" class="form-control"
                                    name="new_bronnen_to_existing[POINT_ID][TEMP_ID][catalogusnummer]"
                                    placeholder="12345">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Referentie tekst:</label>
                        <textarea class="form-control"
                            name="new_bronnen_to_existing[POINT_ID][TEMP_ID][referentie_tekst]" rows="2"
                            placeholder="Referentie tekst"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Bron Afbeelding:</label>
                        <div class="file-upload-container" style="margin-bottom: 10px;">
                            <input type="file" class="file-upload-input new-existing-bron-file"
                                name="new_existing_bron_afbeelding[POINT_ID][TEMP_ID]" accept="image/*">
                            <button type="button" class="btn btn-secondary upload-btn">
                                <i class="fas fa-upload"></i> Kies afbeelding
                            </button>
                            <span class="file-name">Geen bestand gekozen</span>
                        </div>
                        <input type="text" class="form-control" style="margin-top: 10px;"
                            placeholder="of voer een URL in"
                            name="new_bronnen_to_existing[POINT_ID][TEMP_ID][bron-afbeelding]">
                        <div class="bron-image-preview new-existing-bron-preview" style="margin-top: 10px;">
                            <p class="text-muted" style="font-size: 12px;">
                                Geen afbeelding geselecteerd
                            </p>
                        </div>
                        <small class="text-muted">Upload een bestand of voer een URL in</small>
                    </div>
                </div>
            </div>
        </div>

        <form method="post" id="editPanoramaForm" enctype="multipart/form-data">
            <!-- Gebruik panorama_id als hidden field -->
            <input type="hidden" name="id" value="<?php echo $panorama_id; ?>">

            <!-- Panorama Details -->
            <div class="form-section">
                <h3>Panorama Details</h3>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="titel" class="form-label">Titel:</label>
                            <input type="text" class="form-control" id="titel" name="titel"
                                value="<?php echo isset($panorama_row['titel']) ? htmlspecialchars($panorama_row['titel']) : ''; ?>"
                                required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="catalogusnummer" class="form-label">Catalogusnummer:</label>
                            <input type="text" class="form-control" id="catalogusnummer" name="catalogusnummer"
                                value="<?php echo isset($panorama_row['catalogusnummer']) ? htmlspecialchars($panorama_row['catalogusnummer']) : ''; ?>">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="beschrijving" class="form-label">Beschrijving:</label>
                    <textarea class="form-control" id="beschrijving" name="beschrijving"
                        rows="3"><?php echo isset($panorama_row['beschrijving']) ? htmlspecialchars($panorama_row['beschrijving']) : ''; ?></textarea>
                </div>

                <div class="mb-3">
                    <label for="afbeelding_url" class="form-label"></label>
                    <input type="hidden" class="form-control" id="afbeelding_url" name="afbeelding_url"
                        value="<?php echo isset($panorama_row['afbeelding_url']) ? htmlspecialchars($panorama_row['afbeelding_url']) : ''; ?>"
                        required>
                </div>
            </div>

            <!-- Panorama Preview -->
            <div class="form-section">
                <h3>Panorama Preview</h3>
                <p style="color: #666; margin-bottom: 15px;">Sleep de punten om ze te verplaatsen</p>

                <div class="panorama-container">
                    <div class="panorama-strip">
                        <div class="panorama" data-panorama-id="<?php echo $panorama_id; ?>"
                            style="display: inline-block; position: relative;">
                            <?php if (isset($panorama_row['afbeelding_url'])): ?>
                                <img src="<?php echo htmlspecialchars($panorama_row['afbeelding_url']); ?>"
                                    alt="<?php echo isset($panorama_row['titel']) ? htmlspecialchars($panorama_row['titel']) : ''; ?>">

                                <?php if (!empty($punten)): ?>
                                    <?php foreach ($punten as $punt): ?>
                                        <?php if ($punt['status'] === 'gepubliceerd'): ?>
                                            <button class="punt" data-x="<?php echo $punt['x']; ?>" data-y="<?php echo $punt['y']; ?>"
                                                data-panorama-id="<?php echo $punt['panorama_id']; ?>"
                                                data-punt-id="<?php echo $punt['id']; ?>"
                                                title="<?php echo htmlspecialchars($punt['titel']); ?>" type="button">
                                                <span class="punt-dot"></span>
                                            </button>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Existing Points Editing -->
            <div class="form-section">
                <div class="action-header">
                    <h3>Punten Bewerken</h3>
                    <button type="button" id="addPointButton" class="btn btn-success">
                        + Nieuw Punt Toevoegen
                    </button>
                </div>

                <?php if (!empty($punten)): ?>
                    <?php foreach ($punten as $punt): ?>
                        <?php
                        // Zorg dat status altijd een geldige waarde heeft
                        $punt_status = $punt['status'];
                        if (!in_array($punt_status, ['concept', 'gepubliceerd', 'gearchiveerd'])) {
                            $punt_status = 'concept';
                        }
                        ?>
                        <div class="point-edit-card" data-punt-id="<?php echo $punt['id']; ?>">
                            <div class="action-header">
                                <h4>Punt <?php echo $punt['id']; ?>: <?php echo htmlspecialchars($punt['titel']); ?></h4>
                                <div class="point-actions">
                                    <span class="status-badge status-<?php echo $punt_status; ?>">
                                        <?php echo $punt_status; ?>
                                    </span>
                                    <select class="status-select" name="punten[<?php echo $punt['id']; ?>][status]">
                                        <option value="concept" <?php echo $punt_status === 'concept' ? 'selected' : ''; ?>>
                                            Concept</option>
                                        <option value="gepubliceerd" <?php echo $punt_status === 'gepubliceerd' ? 'selected' : ''; ?>>Gepubliceerd</option>
                                        <option value="gearchiveerd" <?php echo $punt_status === 'gearchiveerd' ? 'selected' : ''; ?>>Gearchiveerd</option>
                                    </select>

                                    <!-- DELETE button - GECORRIGEERD -->
                                    <a href="bewerk.php?panorama_id=<?php echo $panorama_id; ?>&delete_item_id=<?php echo $punt['id']; ?>&type=punt"
                                        class="delete-btn"
                                        onclick="return confirm('Weet je zeker dat je dit punt wilt verwijderen?')">
                                        × Verwijder
                                    </a>

                                    <!-- PUBLISH button - GECORRIGEERD -->
                                    <a href="bewerk.php?panorama_id=<?php echo $panorama_id; ?>&change_status=1&item_id=<?php echo $punt['id']; ?>&type=punt&status=gepubliceerd"
                                        class="btn btn-small btn-success">
                                        Publiceer
                                    </a>

                                    <!-- ARCHIVE button -->
                                    <a href="bewerk.php?panorama_id=<?php echo $panorama_id; ?>&change_status=1&item_id=<?php echo $punt['id']; ?>&type=punt&status=gearchiveerd"
                                        class="btn btn-small btn-secondary">
                                        Archiveer
                                    </a>

                                    <!-- CONCEPT button -->
                                    <a href="bewerk.php?panorama_id=<?php echo $panorama_id; ?>&change_status=1&item_id=<?php echo $punt['id']; ?>&type=punt&status=concept"
                                        class="btn btn-small btn-warning">
                                        Concept
                                    </a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="punt_titel_<?php echo $punt['id']; ?>" class="form-label">Titel:</label>
                                        <input type="text" class="form-control" id="punt_titel_<?php echo $punt['id']; ?>"
                                            name="punten[<?php echo $punt['id']; ?>][titel]"
                                            value="<?php echo htmlspecialchars($punt['titel']); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="punt_omschrijving_<?php echo $punt['id']; ?>"
                                    class="form-label">Omschrijving:</label>
                                <textarea class="form-control" id="punt_omschrijving_<?php echo $punt['id']; ?>"
                                    name="punten[<?php echo $punt['id']; ?>][omschrijving]"
                                    rows="3"><?php echo htmlspecialchars($punt['omschrijving']); ?></textarea>
                            </div>

                            <div class="coord-inputs">
                                <div class="input-group">
                                    <label for="punt_x_<?php echo $punt['id']; ?>" class="form-label">X-coördinaat:</label>
                                    <input type="number" class="form-control coord-x-input"
                                        id="punt_x_<?php echo $punt['id']; ?>" name="punten[<?php echo $punt['id']; ?>][x]"
                                        value="<?php echo $punt['x']; ?>">
                                </div>
                                <div class="input-group">
                                    <label for="punt_y_<?php echo $punt['id']; ?>" class="form-label">Y-coördinaat:</label>
                                    <input type="number" class="form-control coord-y-input"
                                        id="punt_y_<?php echo $punt['id']; ?>" name="punten[<?php echo $punt['id']; ?>][y]"
                                        value="<?php echo $punt['y']; ?>">
                                </div>
                            </div>

                            <!-- Existing Sources -->
                            <?php if (!empty($punt['bronnen'])): ?>
                                <div class="mt-4">
                                    <div class="action-header">
                                        <h5>Bronnen</h5>
                                        <button type="button" class="btn btn-success add-bron-to-existing"
                                            data-point-id="<?php echo $punt['id']; ?>">
                                            + Nieuwe Bron Toevoegen
                                        </button>
                                    </div>
                                    <?php foreach ($punt['bronnen'] as $bron): ?>
                                        <?php
                                        // Zorg dat bron status altijd een geldige waarde heeft
                                        $bron_status = $bron['status'];
                                        if (!in_array($bron_status, ['concept', 'gepubliceerd', 'gearchiveerd'])) {
                                            $bron_status = 'concept';
                                        }
                                        ?>
                                        <div class="source-edit-card">
                                            <div class="action-header">
                                                <h6>Bron <?php echo $bron['id']; ?></h6>
                                                <div class="point-actions">
                                                    <span class="status-badge status-<?php echo $bron_status; ?>">
                                                        <?php echo $bron_status; ?>
                                                    </span>
                                                    <select class="status-select" name="bronnen[<?php echo $bron['id']; ?>][status]">
                                                        <option value="concept" <?php echo $bron_status === 'concept' ? 'selected' : ''; ?>>Concept</option>
                                                        <option value="gepubliceerd" <?php echo $bron_status === 'gepubliceerd' ? 'selected' : ''; ?>>Gepubliceerd</option>
                                                        <option value="gearchiveerd" <?php echo $bron_status === 'gearchiveerd' ? 'selected' : ''; ?>>Gearchiveerd</option>
                                                    </select>

                                                    <!-- DELETE button voor bron - GECORRIGEERD -->
                                                    <a href="bewerk.php?panorama_id=<?php echo $panorama_id; ?>&delete_item_id=<?php echo $bron['id']; ?>&type=bron"
                                                        class="delete-btn"
                                                        onclick="return confirm('Weet je zeker dat je deze bron wilt verwijderen?')">
                                                        × Verwijder
                                                    </a>

                                                    <!-- PUBLISH button voor bron - GECORRIGEERD -->
                                                    <a href="bewerk.php?panorama_id=<?php echo $panorama_id; ?>&change_status=1&item_id=<?php echo $bron['id']; ?>&type=bron&status=gepubliceerd"
                                                        class="btn btn-small btn-success">
                                                        Publiceer
                                                    </a>

                                                    <!-- ARCHIVE button voor bron -->
                                                    <a href="bewerk.php?panorama_id=<?php echo $panorama_id; ?>&change_status=1&item_id=<?php echo $bron['id']; ?>&type=bron&status=gearchiveerd"
                                                        class="btn btn-small btn-secondary">
                                                        Archiveer
                                                    </a>

                                                    <!-- CONCEPT button voor bron -->
                                                    <a href="bewerk.php?panorama_id=<?php echo $panorama_id; ?>&change_status=1&item_id=<?php echo $bron['id']; ?>&type=bron&status=concept"
                                                        class="btn btn-small btn-warning">
                                                        Concept
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="bron_titel_<?php echo $bron['id']; ?>"
                                                            class="form-label">Titel:</label>
                                                        <input type="text" class="form-control"
                                                            id="bron_titel_<?php echo $bron['id']; ?>"
                                                            name="bronnen[<?php echo $bron['id']; ?>][titel]"
                                                            value="<?php echo htmlspecialchars($bron['titel']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="bron_auteur_<?php echo $bron['id']; ?>"
                                                            class="form-label">Auteur:</label>
                                                        <input type="text" class="form-control"
                                                            id="bron_auteur_<?php echo $bron['id']; ?>"
                                                            name="bronnen[<?php echo $bron['id']; ?>][auteur]"
                                                            value="<?php echo htmlspecialchars($bron['auteur']); ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Type:</label>
                                                        <select class="form-control"
                                                            name="bronnen[<?php echo $bron['id']; ?>][bron_type]">
                                                            <option value="boek" <?php echo $bron['bron_type'] === 'boek' ? 'selected' : ''; ?>>Boek</option>
                                                            <option value="artikel" <?php echo $bron['bron_type'] === 'artikel' ? 'selected' : ''; ?>>Artikel</option>
                                                            <option value="website" <?php echo $bron['bron_type'] === 'website' ? 'selected' : ''; ?>>Website</option>
                                                            <option value="video" <?php echo $bron['bron_type'] === 'video' ? 'selected' : ''; ?>>Video</option>
                                                            <option value="document" <?php echo $bron['bron_type'] === 'document' ? 'selected' : ''; ?>>Document</option>
                                                            <option value="ander" <?php echo $bron['bron_type'] === 'ander' ? 'selected' : ''; ?>>Ander</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Publicatiejaar:</label>
                                                        <input type="text" class="form-control"
                                                            name="bronnen[<?php echo $bron['id']; ?>][publicatie_jaar]"
                                                            value="<?php echo htmlspecialchars($bron['publicatie_jaar']); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <label class="form-label">Catalogusnummer:</label>
                                                        <input type="text" class="form-control"
                                                            name="bronnen[<?php echo $bron['id']; ?>][catalogusnummer]"
                                                            value="<?php echo htmlspecialchars($bron['catalogusnummer']); ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="bron_referentie_<?php echo $bron['id']; ?>" class="form-label">Referentie
                                                    tekst:</label>
                                                <textarea class="form-control" id="bron_referentie_<?php echo $bron['id']; ?>"
                                                    name="bronnen[<?php echo $bron['id']; ?>][referentie_tekst]"
                                                    rows="2"><?php echo htmlspecialchars($bron['referentie_tekst']); ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Bron Afbeelding:</label>

                                                <?php if (!empty($bron['bron-afbeelding'])): ?>
                                                    <div style="margin-bottom: 10px;">
                                                        <img src="<?php echo htmlspecialchars($bron['bron-afbeelding']); ?>"
                                                            alt="Huidige afbeelding"
                                                            style="max-width: 200px; display: block; margin-bottom: 10px;">
                                                        <small>Huidige afbeelding: <?php echo basename($bron['bron-afbeelding']); ?></small>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="file-upload-container" style="margin-bottom: 10px;">
                                                    <input type="file" class="file-upload-input existing-bron-file"
                                                        name="bron_afbeelding_<?php echo $bron['id']; ?>"
                                                        data-bron-id="<?php echo $bron['id']; ?>" accept="image/*">
                                                    <button type="button" class="btn btn-secondary upload-btn">
                                                        <i class="fas fa-upload"></i> Upload nieuwe afbeelding
                                                    </button>
                                                    <span class="file-name">Geen bestand gekozen</span>
                                                </div>

                                                <input type="text" class="form-control" id="bron_afbeelding_<?php echo $bron['id']; ?>"
                                                    name="bronnen[<?php echo $bron['id']; ?>][bron-afbeelding]"
                                                    value="<?php echo htmlspecialchars($bron['bron-afbeelding']); ?>"
                                                    placeholder="Afbeelding URL">
                                                <small class="text-muted">Upload een bestand of voer een URL in</small>

                                                <div class="bron-image-preview existing-bron-preview"
                                                    id="existing_bron_preview_<?php echo $bron['id']; ?>" style="margin-top: 10px;">
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="new-bronnen-to-existing-container" data-point-id="<?php echo $punt['id']; ?>"
                                        style="margin-top: 15px;">
                                        <!-- New sources for existing points will be added here -->
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="mt-4">
                                    <div class="action-header">
                                        <h5>Bronnen</h5>
                                        <button type="button" class="btn btn-success add-bron-to-existing"
                                            data-point-id="<?php echo $punt['id']; ?>">
                                            + Nieuwe Bron Toevoegen
                                        </button>
                                    </div>
                                    <div class="new-bronnen-to-existing-container" data-point-id="<?php echo $punt['id']; ?>"
                                        style="margin-top: 15px;">
                                        <!-- New sources for existing points will be added here -->
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Geen punten gevonden voor dit panorama.</p>
                <?php endif; ?>

                <!-- Container for new points -->
                <div id="newPointsContainer" style="margin-top: 30px;">
                    <!-- New points will be added here by JavaScript -->
                </div>
            </div>

            <div class="form-actions">
                <a href="admin.php" class="btn btn-secondary">Terug naar Overzicht</a>
                <button type="submit" class="btn btn-primary">Alle Wijzigingen Opslaan</button>
            </div>
        </form>
    </div>

    <?php include 'assets/includes/footer.php'; ?>

    <!-- JavaScript files -->
    <script src="assets/js/recoords.js"></script>
    <script src="assets/js/bewerk.js"></script>
</body>

</html>