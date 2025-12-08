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

// Haal bestaand record
$stmt1 = $conn->prepare("SELECT * FROM panorama WHERE id = ?");
$stmt1->bind_param("i", $id);
$stmt1->execute();
$result1 = $stmt1->get_result();
$row = $result1->fetch_assoc();
if (!$row) {
    echo "Niet gevonden";
    exit;
}

// Haal punt op
$stmt3 = $conn->prepare("SELECT * FROM punten WHERE panorama_id = ?");
$stmt3->bind_param("i", $id);
$stmt3->execute();
$result3 = $stmt3->get_result();
$row = $result3->fetch_assoc();



// Haal bronnen voor dit punt
$stmt_bronnen = $conn->prepare("SELECT * FROM bronnen WHERE punt_id = ?");
$stmt_bronnen->bind_param("i", $id);
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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $x_coordinaat = $_POST['x_coordinaat'];
    $y_coordinaat = $_POST['y_coordinaat'];
    $hoogte = $_POST['hoogte'];
    $breedte = $_POST['breedte'];
    $panorama_id = $_POST['panorama_id'];

    // Update punten
    $stmt3 = $conn->prepare("UPDATE punten SET x_coordinaat=?, y_coordinaat=?, hoogte=?, breedte=?, panorama_id=?  WHERE id=?");
    $stmt3->bind_param("sssssi", $x_coordinaat, $y_coordinaat, $hoogte, $breedte, $panorama_id, $id);
    $stmt3->execute();

    // Update bronnen
    if (isset($_POST['bronnen']) && is_array($_POST['bronnen'])) {
        foreach ($_POST['bronnen'] as $bron_id => $bron_data) {
            if (isset($bron_data['referentie_tekst'])) {
                $referentie_tekst = $bron_data['referentie_tekst'];
                $stmt2 = $conn->prepare("UPDATE bronnen SET referentie_tekst=? WHERE id=?");
                $stmt2->bind_param("si", $referentie_tekst, $bron_id);
                $stmt2->execute();
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
    <title>Bewerk Punt</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include "assets/includes/header.php"; ?>

    <div class="tabel">
        <form method="post" class="mb-3">
            <?php if (!empty($bronnen)): ?>
                <br></br>
                <h2>Bewerk Punt</h2>
                <div class="mb-3">
                    <label for="panorama_id" class="form-label">pagina:</label>
                    <input type="text" class="form-control" id="panorama_id" name="panorama_id"
                        value="<?php echo htmlspecialchars($row['panorama_id']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="x_coordinaat" class="form-label">x_coordinaat:</label>
                    <input type="text" class="form-control" id="x_coordinaat" name="x_coordinaat"
                        value="<?php echo htmlspecialchars($row['x_coordinaat']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="y_coordinaat" class="form-label">y_coordinaat:</label>
                    <input type="text" class="form-control" id="y_coordinaat" name="y_coordinaat"
                        value="<?php echo htmlspecialchars($row['y_coordinaat']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="hoogte" class="form-label">hoogte:</label>
                    <input type="text" class="form-control" id="hoogte" name="hoogte"
                        value="<?php echo htmlspecialchars($row['hoogte']); ?>" required>
                </div>

                <div class="mb-3">
                    <label for="breedte" class="form-label">breedte:</label>
                    <input type="text" class="form-control" id="breedte" name="breedte"
                        value="<?php echo htmlspecialchars($row['breedte']); ?>" required>
                </div>

                <?php foreach ($bronnen as $bron): ?>
                    <?php if (is_array($bron) && isset($bron['id'])): ?>

                        <h2>bronnen</h2>
                        <div class="mb-3">
                            <label for="bronnen[<?php echo htmlspecialchars($bron['id'] ?? ''); ?>][titel]"
                                class="form-label"><br>Id: <?php echo htmlspecialchars($bron['id'] ?? ''); ?>:</br> Titel voor
                                bron:</label>
                            <input type="text" class="form-control"
                                id="bronnen[<?php echo htmlspecialchars($bron['id'] ?? ''); ?>][titel]"
                                name="bronnen[<?php echo htmlspecialchars($bron['id'] ?? ''); ?>][titel]"
                                value="<?php echo htmlspecialchars($bron['titel'] ?? ''); ?>">
                        </div>

                        <div class="mb-3">
                            <label for="bronnen[<?php echo htmlspecialchars($bron['id'] ?? ''); ?>][referentie_tekst]"
                                class="form-label">Referentie tekst voor bron:</label>
                            <input type="text" class="form-control"
                                id="bronnen[<?php echo htmlspecialchars($bron['id'] ?? ''); ?>][referentie_tekst]"
                                name="bronnen[<?php echo htmlspecialchars($bron['id'] ?? ''); ?>][referentie_tekst]"
                                value="<?php echo htmlspecialchars($bron['referentie_tekst'] ?? ''); ?>">
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>

            <button type="submit" class="btn btn-primary">Opslaan</button>
            <a href="admin.php" class="btn btn-secondary">Terug</a>
        </form>

        <?php include "assets/includes/footer.php"; ?>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </div>


</body>

</html>