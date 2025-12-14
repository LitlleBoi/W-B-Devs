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

// Haal panorama gegevens op (dit is wat je nodig hebt voor de bewerkpagina)
$stmt_panorama = $conn->prepare("SELECT * FROM panorama WHERE id = ?");
$stmt_panorama->bind_param("i", $id);
$stmt_panorama->execute();
$result_panorama = $stmt_panorama->get_result();
$panorama_row = $result_panorama->fetch_assoc();

if (!$panorama_row) {
    echo "Panorama niet gevonden";
    exit;
}

// Haal ALLE punten op voor dit panorama (niet slechts één)
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
                ];
            }
        }

        $punten[] = [
            'id' => $punt_id,
            'x_coordinaat' => $punt_row['x_coordinaat'],
            'y_coordinaat' => $punt_row['y_coordinaat'],
            'panorama_id' => $punt_row['panorama_id'],
            'bronnen' => $bronnen
        ];
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Update panorama basisgegevens
    $titel = $_POST['titel'] ?? '';
    $beschrijving = $_POST['beschrijving'] ?? '';
    $catalogusnummer = $_POST['catalogusnummer'] ?? '';

    $stmt_update_panorama = $conn->prepare("UPDATE panorama SET titel = ?, beschrijving = ?, catalogusnummer = ? WHERE id = ?");
    $stmt_update_panorama->bind_param("sssi", $titel, $beschrijving, $catalogusnummer, $id);
    $stmt_update_panorama->execute();

    // Update punten als deze bestaan
    if (isset($_POST['punten']) && is_array($_POST['punten'])) {
        foreach ($_POST['punten'] as $punt_id => $punt_data) {
            if (isset($punt_data['x_coordinaat']) && isset($punt_data['y_coordinaat'])) {
                $stmt_update_punt = $conn->prepare("UPDATE punten SET x_coordinaat = ?, y_coordinaat = ? WHERE id = ? AND panorama_id = ?");
                $stmt_update_punt->bind_param("ssii", $punt_data['x_coordinaat'], $punt_data['y_coordinaat'], $punt_id, $id);
                $stmt_update_punt->execute();
            }
        }
    }

    // Update bronnen
    if (isset($_POST['bronnen']) && is_array($_POST['bronnen'])) {
        foreach ($_POST['bronnen'] as $bron_id => $bron_data) {
            if (isset($bron_data['referentie_tekst']) && isset($bron_data['titel'])) {
                $stmt_update_bron = $conn->prepare("UPDATE bronnen SET referentie_tekst = ?, titel = ? WHERE id = ?");
                $stmt_update_bron->bind_param("ssi", $bron_data['referentie_tekst'], $bron_data['titel'], $bron_id);
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
</head>

<body>
    <?php include "assets/includes/header.php"; ?>

    <div class="tabel">
        <form method="post" class="mb-3">
            <h2>Bewerk Panorama</h2>

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
                <input type="text" class="form-control" id="beschrijving" name="beschrijving"
                    value="<?php echo htmlspecialchars($panorama_row['beschrijving'] ?? ''); ?>">
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
                                <label for="punten[<?php echo $punt['id']; ?>][x_coordinaat]"
                                    class="form-label">X-coördinaat:</label>
                                <input type="text" class="form-control" id="punten[<?php echo $punt['id']; ?>][x_coordinaat]"
                                    name="punten[<?php echo $punt['id']; ?>][x_coordinaat]"
                                    value="<?php echo htmlspecialchars($punt['x_coordinaat']); ?>">
                            </div>

                            <div class="mb-3">
                                <label for="punten[<?php echo $punt['id']; ?>][y_coordinaat]"
                                    class="form-label">Y-coördinaat:</label>
                                <input type="text" class="form-control" id="punten[<?php echo $punt['id']; ?>][y_coordinaat]"
                                    name="punten[<?php echo $punt['id']; ?>][y_coordinaat]"
                                    value="<?php echo htmlspecialchars($punt['y_coordinaat']); ?>">
                            </div>

                            <?php if (!empty($punt['bronnen'])): ?>
                                <h4>Bronnen voor dit punt</h4>
                                <?php foreach ($punt['bronnen'] as $bron): ?>
                                    <div class="mb-3">
                                        <label for="bronnen[<?php echo $bron['id']; ?>][titel]" class="form-label">Titel voor
                                            bron:</label>
                                        <input type="text" class="form-control" id="bronnen[<?php echo $bron['id']; ?>][titel]"
                                            name="bronnen[<?php echo $bron['id']; ?>][titel]"
                                            value="<?php echo htmlspecialchars($bron['titel'] ?? ''); ?>">
                                    </div>

                                    <div class="mb-3">
                                        <label for="bronnen[<?php echo $bron['id']; ?>][referentie_tekst]" class="form-label">Referentie
                                            tekst:</label>
                                        <input type="text" class="form-control"
                                            id="bronnen[<?php echo $bron['id']; ?>][referentie_tekst]"
                                            name="bronnen[<?php echo $bron['id']; ?>][referentie_tekst]"
                                            value="<?php echo htmlspecialchars($bron['referentie_tekst'] ?? ''); ?>">
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