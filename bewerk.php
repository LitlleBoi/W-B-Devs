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

// Debug: Show the ID we're trying to fetch
echo "<!-- Debug: Looking for panorama with ID: $id -->";

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

// Debug: Check what we got from database
echo "<!-- Debug: panorama_row = " . print_r($panorama_row, true) . " -->";

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
                            'afbeelding' => $bron_row['afbeelding'] ?? '',
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

// Debug: Check what points we got
echo "<!-- Debug: punten count = " . count($punten) . " -->";
echo "<!-- Debug: punten = " . print_r($punten, true) . " -->";

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

    // Update bronnen
    if (isset($_POST['bronnen']) && is_array($_POST['bronnen'])) {
        foreach ($_POST['bronnen'] as $bron_id => $bron_data) {
            $referentie_tekst = $bron_data['referentie_tekst'] ?? '';
            $titel = $bron_data['titel'] ?? '';
            $auteur = $bron_data['auteur'] ?? '';
            $afbeelding_url = $bron_data['afbeelding'] ?? '';

            $stmt_update_bron = $conn->prepare("UPDATE bronnen SET referentie_tekst = ?, titel = ?, auteur = ?, afbeelding = ? WHERE id = ?");
            if ($stmt_update_bron) {
                $stmt_update_bron->bind_param("ssssi", $referentie_tekst, $titel, $auteur, $afbeelding_url, $bron_id);
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
    <style>
        .tabel {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .coord-inputs {
            display: flex;
            gap: 10px;
        }

        .coord-inputs .form-group {
            flex: 1;
        }
    </style>
</head>

<body>
    <?php include 'assets/includes/header.php'; ?>

    <div class="tabel">
        <form method="post" id="editPanoramaForm">
            <h2>Bewerk Panorama</h2>
            <input type="hidden" name="id" value="<?php echo $id; ?>">

            <!-- Panorama Details -->
            <div class="mb-3">
                <label for="titel" class="form-label">Titel:</label>
                <input type="text" class="form-control" id="titel" name="titel"
                    value="<?php echo isset($panorama_row['titel']) ? htmlspecialchars($panorama_row['titel']) : ''; ?>"
                    required>
            </div>

            <div class="mb-3">
                <label for="catalogusnummer" class="form-label">Catalogusnummer:</label>
                <input type="text" class="form-control" id="catalogusnummer" name="catalogusnummer"
                    value="<?php echo isset($panorama_row['catalogusnummer']) ? htmlspecialchars($panorama_row['catalogusnummer']) : ''; ?>">
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

            <!-- Panorama Preview with points -->
            <div class="panorama-container"
                style="height: 500px; overflow: auto; background: #f0f0f0; margin-bottom: 20px;">
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

            <!-- Points Editing Form -->
            <div class="points-edit-section mt-4">
                <h3>Punten Bewerken</h3>

                <?php if (!empty($punten)): ?>
                    <?php foreach ($punten as $punt): ?>
                        <div class="point-edit-card mb-3 p-3 border rounded" data-punt-id="<?php echo $punt['id']; ?>">
                            <h4>Punt <?php echo $punt['id']; ?></h4>

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

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="punt_x_<?php echo $punt['id']; ?>" class="form-label">X-coördinaat:</label>
                                        <input type="number" class="form-control coord-x-input"
                                            id="punt_x_<?php echo $punt['id']; ?>" name="punten[<?php echo $punt['id']; ?>][x]"
                                            value="<?php echo $punt['x']; ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="punt_y_<?php echo $punt['id']; ?>" class="form-label">Y-coördinaat:</label>
                                        <input type="number" class="form-control coord-y-input"
                                            id="punt_y_<?php echo $punt['id']; ?>" name="punten[<?php echo $punt['id']; ?>][y]"
                                            value="<?php echo $punt['y']; ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Sources -->
                            <?php if (!empty($punt['bronnen'])): ?>
                                <h5 class="mt-3">Bronnen</h5>
                                <?php foreach ($punt['bronnen'] as $bron): ?>
                                    <div class="source-edit-card p-3 border rounded mb-2">
                                        <h6>Bron <?php echo $bron['id']; ?></h6>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="bron_titel_<?php echo $bron['id']; ?>" class="form-label">Titel:</label>
                                                    <input type="text" class="form-control" id="bron_titel_<?php echo $bron['id']; ?>"
                                                        name="bronnen[<?php echo $bron['id']; ?>][titel]"
                                                        value="<?php echo htmlspecialchars($bron['titel']); ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="bron_auteur_<?php echo $bron['id']; ?>"
                                                        class="form-label">Auteur:</label>
                                                    <input type="text" class="form-control" id="bron_auteur_<?php echo $bron['id']; ?>"
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
                                            <label for="bron_afbeelding_<?php echo $bron['id']; ?>" class="form-label">Afbeelding
                                                URL:</label>
                                            <input type="text" class="form-control" id="bron_afbeelding_<?php echo $bron['id']; ?>"
                                                name="bronnen[<?php echo $bron['id']; ?>][afbeelding]"
                                                value="<?php echo htmlspecialchars($bron['afbeelding']); ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Geen punten gevonden voor dit panorama.</p>
                <?php endif; ?>
            </div>

            <div class="form-actions mt-4">
                <button type="submit" class="btn btn-primary">Opslaan</button>
                <a href="admin.php" class="btn btn-secondary">Terug</a>
            </div>
        </form>
    </div>

    <?php include 'assets/includes/footer.php'; ?>

    // Replace the JavaScript section in bewerk.php with this:

    <script src="assets/js/recoords.js"></script>

    <!-- Custom JavaScript for edit page -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const panoramaImage = document.querySelector('.panorama img');
            const imageUrlInput = document.getElementById('afbeelding_url');
            const points = document.querySelectorAll('.punt');

            // Update image when URL changes
            if (imageUrlInput && panoramaImage) {
                imageUrlInput.addEventListener('change', function () {
                    panoramaImage.src = this.value;
                    // Trigger point repositioning after image loads
                    panoramaImage.onload = function () {
                        setTimeout(updatePointPositions, 100);
                    };
                });
            }

            // Make points draggable and update form inputs
            if (points.length > 0) {
                points.forEach(point => {
                    // Remove any existing event listeners
                    point.removeEventListener('mousedown', startDrag);

                    point.addEventListener('mousedown', startDrag);

                    // Prevent default button behavior
                    point.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                    });
                });
            }

            function startDrag(e) {
                e.preventDefault();
                e.stopPropagation();

                const point = e.currentTarget;
                const pointId = point.getAttribute('data-punt-id');
                const xInput = document.querySelector(`[name="punten[${pointId}][x]"]`);
                const yInput = document.querySelector(`[name="punten[${pointId}][y]"]`);
                const panorama = point.closest('.panorama');
                const img = panorama.querySelector('img');

                // Get current position from data attributes (absolute pixel values)
                const currentX = parseFloat(point.getAttribute('data-x'));
                const currentY = parseFloat(point.getAttribute('data-y'));

                // Get image natural dimensions
                const naturalWidth = img.naturalWidth;
                const naturalHeight = img.naturalHeight;

                // Get displayed image dimensions
                const displayedWidth = img.offsetWidth;
                const displayedHeight = img.offsetHeight;

                // Calculate scale factors
                const scaleX = displayedWidth / naturalWidth;
                const scaleY = displayedHeight / naturalHeight;

                // Convert absolute coordinates to displayed coordinates
                const displayedX = currentX * scaleX;
                const displayedY = currentY * scaleY;

                // Get mouse starting position
                const startMouseX = e.clientX;
                const startMouseY = e.clientY;

                // Get current displayed position (what user sees)
                const currentDisplayedX = displayedX;
                const currentDisplayedY = displayedY;

                function doDrag(e) {
                    // Calculate mouse movement
                    const deltaX = e.clientX - startMouseX;
                    const deltaY = e.clientY - startMouseY;

                    // Calculate new displayed position
                    let newDisplayedX = currentDisplayedX + deltaX;
                    let newDisplayedY = currentDisplayedY + deltaY;

                    // Constrain to image bounds
                    newDisplayedX = Math.max(0, Math.min(newDisplayedX, displayedWidth));
                    newDisplayedY = Math.max(0, Math.min(newDisplayedY, displayedHeight));

                    // Convert displayed position back to natural coordinates
                    const newNaturalX = newDisplayedX / scaleX;
                    const newNaturalY = newDisplayedY / scaleY;

                    // Update data attributes with natural coordinates
                    point.setAttribute('data-x', Math.round(newNaturalX));
                    point.setAttribute('data-y', Math.round(newNaturalY));

                    // Update form inputs
                    if (xInput) xInput.value = Math.round(newNaturalX);
                    if (yInput) yInput.value = Math.round(newNaturalY);

                    // Recalculate point position using the existing recoords.js function
                    // We'll manually update the point position for immediate feedback
                    const xPercent = (newNaturalX / naturalWidth) * 100;
                    const yPercent = (newNaturalY / naturalHeight) * 100;

                    point.style.left = xPercent + '%';
                    point.style.top = yPercent + '%';
                    point.style.transform = 'translate(-50%, -50%)';
                }

                function stopDrag() {
                    document.removeEventListener('mousemove', doDrag);
                    document.removeEventListener('mouseup', stopDrag);

                    // After dragging, run updatePointPositions to ensure everything is consistent
                    setTimeout(updatePointPositions, 10);
                }

                document.addEventListener('mousemove', doDrag);
                document.addEventListener('mouseup', stopDrag);
            }

            // Update data attributes when form inputs change
            document.querySelectorAll('.coord-x-input').forEach(input => {
                input.removeEventListener('change', updateCoordFromInput);
                input.addEventListener('change', updateCoordFromInput);
            });

            document.querySelectorAll('.coord-y-input').forEach(input => {
                input.removeEventListener('change', updateCoordFromInput);
                input.addEventListener('change', updateCoordFromInput);
            });

            function updateCoordFromInput(e) {
                const input = e.target;
                const match = input.name.match(/punten\[(\d+)\]\[(x|y)\]/);

                if (match) {
                    const pointId = match[1];
                    const coordType = match[2];
                    const point = document.querySelector(`[data-punt-id="${pointId}"]`);

                    if (point) {
                        // Update data attribute
                        point.setAttribute(`data-${coordType}`, input.value);

                        // Re-run point positioning
                        setTimeout(updatePointPositions, 10);
                    }
                }
            }

            // Also update when image loads
            if (panoramaImage) {
                panoramaImage.addEventListener('load', function () {
                    setTimeout(updatePointPositions, 100);
                });
            }
        });
    </script>
</body>

</html>