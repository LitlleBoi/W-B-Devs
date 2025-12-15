<?php
session_start();

// simple access control: only allow when logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
    header('Location: inlog.php');
    exit();
}

include 'assets/includes/connectie.php';

// Debug: Check if connection works
if (!$conn) {
    die("Database connection failed");
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo "Ongeldig ID";
    exit;
}

// Haal panorama gegevens op
$stmt_panorama = $conn->prepare("SELECT * FROM panorama WHERE id = ?");
if (!$stmt_panorama) {
    die("Prepare failed: " . $conn->error);
}
$stmt_panorama->bind_param("i", $id);
$stmt_panorama->execute();
$result_panorama = $stmt_panorama->get_result();

if (!$result_panorama) {
    die("Query failed: " . $conn->error);
}

$panorama_row = $result_panorama->fetch_assoc();

if (!$panorama_row) {
    echo "Panorama niet gevonden in database voor ID: $id";
    exit;
}

// Haal punten op voor dit panorama
$punten = [];
$stmt_punten = $conn->prepare("SELECT id, x_coordinaat, y_coordinaat, panorama_id, titel, omschrijving FROM punten WHERE panorama_id = ?");
if ($stmt_punten) {
    $stmt_punten->bind_param("i", $id);
    $stmt_punten->execute();
    $result_punten = $stmt_punten->get_result();

    if ($result_punten && $result_punten->num_rows > 0) {
        while ($punt_row = $result_punten->fetch_assoc()) {
            $punt_id = $punt_row['id'];

            // Haal bronnen voor dit specifieke punt op
            $bronnen = [];
            $stmt_bronnen = $conn->prepare("SELECT * FROM bronnen WHERE punt_id = ?");
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
                            'bron-afbeelding' => $bron_row['bron-afbeelding'] ?? '', // Correct column name
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
                'bronnen' => $bronnen
            ];
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Update panorama basisgegevens
    $titel = $_POST['titel'] ?? '';
    $beschrijving = $_POST['beschrijving'] ?? '';
    $catalogusnummer = $_POST['catalogusnummer'] ?? '';
    $afbeelding = $_POST['afbeelding_url'] ?? '';

    $stmt_update_panorama = $conn->prepare("UPDATE panorama SET titel = ?, beschrijving = ?, catalogusnummer = ?, afbeelding_url = ? WHERE id = ?");
    if ($stmt_update_panorama) {
        $stmt_update_panorama->bind_param("ssssi", $titel, $beschrijving, $catalogusnummer, $afbeelding, $id);
        $stmt_update_panorama->execute();
    }

    // Update punten
    if (isset($_POST['punten']) && is_array($_POST['punten'])) {
        foreach ($_POST['punten'] as $punt_id => $punt_data) {
            $x = $punt_data['x'] ?? 0;
            $y = $punt_data['y'] ?? 0;
            $punt_titel = $punt_data['titel'] ?? '';
            $punt_omschrijving = $punt_data['omschrijving'] ?? '';

            $stmt_update_punt = $conn->prepare("UPDATE punten SET x_coordinaat = ?, y_coordinaat = ?, titel = ?, omschrijving = ? WHERE id = ? AND panorama_id = ?");
            if ($stmt_update_punt) {
                $stmt_update_punt->bind_param("iissii", $x, $y, $punt_titel, $punt_omschrijving, $punt_id, $id);
                $stmt_update_punt->execute();
            }
        }
    }

    // Update bronnen - FIXED: Use correct column name 'bron-afbeelding'
    if (isset($_POST['bronnen']) && is_array($_POST['bronnen'])) {
        foreach ($_POST['bronnen'] as $bron_id => $bron_data) {
            $referentie_tekst = $bron_data['referentie_tekst'] ?? '';
            $titel = $bron_data['titel'] ?? '';
            $auteur = $bron_data['auteur'] ?? '';
            $bron_afbeelding = $bron_data['bron-afbeelding'] ?? ''; 

            $stmt_update_bron = $conn->prepare("UPDATE bronnen SET referentie_tekst = ?, titel = ?, auteur = ?, `bron-afbeelding` = ? WHERE id = ?");
            if ($stmt_update_bron) {
                $stmt_update_bron->bind_param("ssssi", $referentie_tekst, $titel, $auteur, $bron_afbeelding, $bron_id);
                $stmt_update_bron->execute();
            }
        }
    }

    header("Location: admin.php");
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

</head>

<body>
    <?php include 'assets/includes/header.php'; ?>

    <div class="edit-form-container">
        <div class="form-header">
            <h2>Bewerk Panorama: <?php echo htmlspecialchars($panorama_row['titel']); ?></h2>
        </div>

        <form method="post" id="editPanoramaForm">
            <input type="hidden" name="id" value="<?php echo $id; ?>">

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
                    <label for="afbeelding_url" class="form-label">Afbeelding URL:</label>
                    <input type="text" class="form-control" id="afbeelding_url" name="afbeelding_url"
                        value="<?php echo isset($panorama_row['afbeelding_url']) ? htmlspecialchars($panorama_row['afbeelding_url']) : ''; ?>"
                        required>
                </div>
            </div>

            <!-- Panorama Preview with points -->
            <div class="form-section">
                <h3>Panorama Preview</h3>
                <p style="color: #666; margin-bottom: 15px;">Sleep de punten om ze te verplaatsen</p>

                <div class="panorama-container">
                    <div class="panorama-strip">
                        <div class="panorama" data-panorama-id="<?php echo $id; ?>"
                            style="display: inline-block; position: relative;">
                            <?php if (isset($panorama_row['afbeelding_url'])): ?>
                                <img src="<?php echo htmlspecialchars($panorama_row['afbeelding_url']); ?>"
                                    alt="<?php echo isset($panorama_row['titel']) ? htmlspecialchars($panorama_row['titel']) : ''; ?>">

                                <?php if (!empty($punten)): ?>
                                    <?php foreach ($punten as $punt): ?>
                                        <button class="punt" data-x="<?php echo $punt['x']; ?>" data-y="<?php echo $punt['y']; ?>"
                                            data-panorama-id="<?php echo $punt['panorama_id']; ?>"
                                            data-punt-id="<?php echo $punt['id']; ?>"
                                            title="<?php echo htmlspecialchars($punt['titel']); ?>" type="button">
                                            <span class="punt-dot"></span>
                                        </button>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Points Editing Form -->
            <?php if (!empty($punten)): ?>
                <div class="form-section">
                    <h3>Punten Bewerken</h3>

                    <?php foreach ($punten as $punt): ?>
                        <div class="point-edit-card" data-punt-id="<?php echo $punt['id']; ?>">
                            <h4>Punt <?php echo $punt['id']; ?>: <?php echo htmlspecialchars($punt['titel']); ?></h4>

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

                            <!-- Sources -->
                            <?php if (!empty($punt['bronnen'])): ?>
                                <div class="mt-4">
                                    <h5 style="color: #2c3e50; margin-bottom: 15px;">Bronnen</h5>
                                    <?php foreach ($punt['bronnen'] as $bron): ?>
                                        <div class="source-edit-card">
                                            <h6>Bron <?php echo $bron['id']; ?></h6>

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

                                            <div class="mb-3">
                                                <label for="bron_referentie_<?php echo $bron['id']; ?>" class="form-label">Referentie
                                                    tekst:</label>
                                                <textarea class="form-control" id="bron_referentie_<?php echo $bron['id']; ?>"
                                                    name="bronnen[<?php echo $bron['id']; ?>][referentie_tekst]"
                                                    rows="2"><?php echo htmlspecialchars($bron['referentie_tekst']); ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="bron_afbeelding_<?php echo $bron['id']; ?>" class="form-label">Bron
                                                    Afbeelding URL:</label>
                                                <input type="text" class="form-control" id="bron_afbeelding_<?php echo $bron['id']; ?>"
                                                    name="bronnen[<?php echo $bron['id']; ?>][bron-afbeelding]"
                                                    value="<?php echo htmlspecialchars($bron['bron-afbeelding']); ?>">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="form-actions">
                <a href="admin.php" class="btn btn-secondary">Terug</a>
                <button type="submit" class="btn btn-primary">Opslaan</button>
            </div>
        </form>
    </div>

    <?php include 'assets/includes/footer.php'; ?>

    <!-- Include recoords.js for point positioning -->
    <script src="assets/js/recoords.js"></script>
    <script src="assets/js/bewerk.js"></script>

</body>

</html>