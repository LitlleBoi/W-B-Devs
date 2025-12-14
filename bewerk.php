<?php
session_start();

// simple access control: only allow when logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
    header('Location: inlog.php');
    exit();
}

include 'assets/includes/connectie.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo "Ongeldig ID";
    exit;
}

// Haal panorama gegevens op
$stmt_panorama = $conn->prepare("SELECT * FROM panorama WHERE id = ?");
$stmt_panorama->bind_param("i", $id);
$stmt_panorama->execute();
$result_panorama = $stmt_panorama->get_result();
$panorama_row = $result_panorama->fetch_assoc();

if (!$panorama_row) {
    echo "Panorama niet gevonden";
    exit;
}

// Haal ALLE punten op voor dit panorama
$stmt_punten = $conn->prepare("SELECT * FROM punten WHERE panorama_id = ?");
$stmt_punten->bind_param("i", $id);
$stmt_punten->execute();
$result_punten = $stmt_punten->get_result();

$punten = [];
if ($result_punten && $result_punten->num_rows > 0) {
    while ($punt_row = $result_punten->fetch_assoc()) {
        $punt_id = $punt_row['id'];

        // Haal bronnen voor dit specifieke punt op
        $stmt_bronnen = $conn->prepare("SELECT * FROM bronnen WHERE punt_id = ?");
        $stmt_bronnen->bind_param("i", $punt_id);
        $stmt_bronnen->execute();
        $result_bronnen = $stmt_bronnen->get_result();

        $bronnen = [];
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

        $punten[] = [
            'id' => $punt_id,
            'x_coordinaat' => $punt_row['x_coordinaat'],
            'y_coordinaat' => $punt_row['y_coordinaat'],
            'panorama_id' => $punt_row['panorama_id'],
            'titel' => $punt_row['titel'] ?? '',
            'omschrijving' => $punt_row['omschrijving'] ?? '',
            'bronnen' => $bronnen
        ];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Update panorama basisgegevens
    $titel = $_POST['titel'] ?? '';
    $beschrijving = $_POST['beschrijving'] ?? '';
    $catalogusnummer = $_POST['catalogusnummer'] ?? '';
    $afbeelding_url = $_POST['afbeelding_url'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    // Add missing hidden input for panorama ID
    $stmt_update_panorama = $conn->prepare("UPDATE panorama SET titel = ?, beschrijving = ?, catalogusnummer = ?, afbeelding_url = ? WHERE id = ?");
    $stmt_update_panorama->bind_param("ssssi", $titel, $beschrijving, $catalogusnummer, $afbeelding_url, $id);
    $stmt_update_panorama->execute();

    // Update punten als deze bestaan
    if (isset($_POST['punten']) && is_array($_POST['punten'])) {
        foreach ($_POST['punten'] as $punt_id => $punt_data) {
            if (isset($punt_data['x_coordinaat']) && isset($punt_data['y_coordinaat'])) {
                // Also update title and description if needed
                $punt_titel = $punt_data['titel'] ?? '';
                $punt_omschrijving = $punt_data['omschrijving'] ?? '';

                $stmt_update_punt = $conn->prepare("UPDATE punten SET x_coordinaat = ?, y_coordinaat = ?, titel = ?, omschrijving = ? WHERE id = ? AND panorama_id = ?");
                $stmt_update_punt->bind_param("ssssii", $punt_data['x_coordinaat'], $punt_data['y_coordinaat'], $punt_titel, $punt_omschrijving, $punt_id, $id);
                $stmt_update_punt->execute();
            }
        }
    }

    // Update bronnen
    if (isset($_POST['bronnen']) && is_array($_POST['bronnen'])) {
        foreach ($_POST['bronnen'] as $bron_id => $bron_data) {
            if (isset($bron_data['referentie_tekst']) && isset($bron_data['titel'])) {
                $bron_auteur = $bron_data['auteur'] ?? '';
                $bron_afbeelding = $bron_data['afbeelding'] ?? '';

                $stmt_update_bron = $conn->prepare("UPDATE bronnen SET referentie_tekst = ?, titel = ?, auteur = ?, afbeelding = ? WHERE id = ?");
                $stmt_update_bron->bind_param("ssssi", $bron_data['referentie_tekst'], $bron_data['titel'], $bron_auteur, $bron_afbeelding, $bron_id);
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
    <title>Bewerk Panorama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bewerk.css">
    <script src="assets/js/recoords.js"></script>
    <script src="assets/js/pop-up.js"></script>
</head>

<body>
    <?php include "assets/includes/header.php"; ?>

    <div class="tabel">
        <form method="post" class="mb-3">
            <h2>Bewerk Panorama</h2>

            <!-- Hidden field for panorama ID -->
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($panorama_row['id'] ?? ''); ?>">

            <div class="panorama">
                <img src="<?php echo htmlspecialchars($panorama_row['afbeelding_url'] ?? ''); ?>"
                    alt="Panorama Afbeelding" style="">

                <?php foreach ($punten as $punt): ?>
                    <?php if ($punt['panorama_id'] == $panorama_row['id']): ?>
                        <button type="button" class="punt"
                            style="position: absolute; left: <?php echo $punt['x_coordinaat']; ?>px; top: <?php echo $punt['y_coordinaat']; ?>px;"
                            data-punt-id="<?php echo $punt['id']; ?>" title="<?php echo htmlspecialchars($punt['titel']); ?>">
                            <span class="punt-dot"></span>
                        </button>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>

            <div class="mb-3">
                <label for="afbeelding_url" class="form-label">Afbeelding URL:</label>
                <input type="text" class="form-control" id="afbeelding_url" name="afbeelding_url"
                    value="<?php echo htmlspecialchars($panorama_row['afbeelding_url'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="titel" class="form-label">Titel:</label>
                <input type="text" class="form-control" id="titel" name="titel"
                    value="<?php echo htmlspecialchars($panorama_row['titel'] ?? ''); ?>" required>
            </div>

            <div class="mb-3">
                <label for="catalogusnummer" class="form-label">Catalogusnummer:</label>
                <input type="text" class="form-control" id="catalogusnummer" name="catalogusnummer"
                    value="<?php echo htmlspecialchars($panorama_row['catalogusnummer'] ?? ''); ?>">
            </div>

            <div class="mb-3">
                <label for="beschrijving" class="form-label">Beschrijving:</label>
                <textarea class="form-control" id="beschrijving" name="beschrijving"
                    rows="3"><?php echo htmlspecialchars($panorama_row['beschrijving'] ?? ''); ?></textarea>
            </div>

            <?php if (!empty($punten)): ?>
                <h3>Punten</h3>
                <?php foreach ($punten as $punt): ?>
                    <div class="card mb-3">
                        <div class="card-header">
                            Punt ID: <?php echo htmlspecialchars($punt['id']); ?>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="punten[<?php echo $punt['id']; ?>][titel]" class="form-label">Punt Titel:</label>
                                <input type="text" class="form-control" id="punten[<?php echo $punt['id']; ?>][titel]"
                                    name="punten[<?php echo $punt['id']; ?>][titel]"
                                    value="<?php echo htmlspecialchars($punt['titel']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="punten[<?php echo $punt['id']; ?>][omschrijving]" class="form-label">Punt
                                    Omschrijving:</label>
                                <textarea class="form-control" id="punten[<?php echo $punt['id']; ?>][omschrijving]"
                                    name="punten[<?php echo $punt['id']; ?>][omschrijving]"
                                    rows="2"><?php echo htmlspecialchars($punt['omschrijving']); ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="punten[<?php echo $punt['id']; ?>][x_coordinaat]"
                                            class="form-label">X-coördinaat:</label>
                                        <input type="number" class="form-control"
                                            id="punten[<?php echo $punt['id']; ?>][x_coordinaat]"
                                            name="punten[<?php echo $punt['id']; ?>][x_coordinaat]"
                                            value="<?php echo htmlspecialchars($punt['x_coordinaat']); ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="punten[<?php echo $punt['id']; ?>][y_coordinaat]"
                                            class="form-label">Y-coördinaat:</label>
                                        <input type="number" class="form-control"
                                            id="punten[<?php echo $punt['id']; ?>][y_coordinaat]"
                                            name="punten[<?php echo $punt['id']; ?>][y_coordinaat]"
                                            value="<?php echo htmlspecialchars($punt['y_coordinaat']); ?>">
                                    </div>
                                </div>
                            </div>

                            <?php if (!empty($punt['bronnen'])): ?>
                                <h4>Bronnen voor dit punt</h4>
                                <?php foreach ($punt['bronnen'] as $bron): ?>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <h5>Bron ID: <?php echo htmlspecialchars($bron['id']); ?></h5>

                                            <div class="mb-3">
                                                <label for="bronnen[<?php echo $bron['id']; ?>][titel]"
                                                    class="form-label">Titel:</label>
                                                <input type="text" class="form-control" id="bronnen[<?php echo $bron['id']; ?>][titel]"
                                                    name="bronnen[<?php echo $bron['id']; ?>][titel]"
                                                    value="<?php echo htmlspecialchars($bron['titel'] ?? ''); ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label for="bronnen[<?php echo $bron['id']; ?>][auteur]"
                                                    class="form-label">Auteur:</label>
                                                <input type="text" class="form-control" id="bronnen[<?php echo $bron['id']; ?>][auteur]"
                                                    name="bronnen[<?php echo $bron['id']; ?>][auteur]"
                                                    value="<?php echo htmlspecialchars($bron['auteur'] ?? ''); ?>">
                                            </div>

                                            <div class="mb-3">
                                                <label for="bronnen[<?php echo $bron['id']; ?>][referentie_tekst]"
                                                    class="form-label">Referentie tekst:</label>
                                                <textarea class="form-control"
                                                    id="bronnen[<?php echo $bron['id']; ?>][referentie_tekst]"
                                                    name="bronnen[<?php echo $bron['id']; ?>][referentie_tekst]"
                                                    rows="2"><?php echo htmlspecialchars($bron['referentie_tekst'] ?? ''); ?></textarea>
                                            </div>

                                            <div class="mb-3">
                                                <label for="bronnen[<?php echo $bron['id']; ?>][afbeelding]"
                                                    class="form-label">Afbeelding URL:</label>
                                                <input type="text" class="form-control"
                                                    id="bronnen[<?php echo $bron['id']; ?>][afbeelding]"
                                                    name="bronnen[<?php echo $bron['id']; ?>][afbeelding]"
                                                    value="<?php echo htmlspecialchars($bron['afbeelding'] ?? ''); ?>">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Geen punten gevonden voor dit panorama.</p>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a href="admin.php" class="btn btn-secondary">Terug</a>
        </form>

        <?php include "assets/includes/footer.php"; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>

</html>