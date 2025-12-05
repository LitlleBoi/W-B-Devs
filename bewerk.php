<?php
session_start();

// simple access control: only allow when logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
	header('Location: inlog.php');
	exit();
}

include 'assets/includes/connectie.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { echo "Ongeldig ID"; exit; }

// Haal bestaand record
$stmt = $conn->prepare("SELECT * FROM punten WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
if (!$row) { echo "Niet gevonden"; exit; }

// Haal bronnen voor dit punt
$bronnen = [];
$stmt = $conn->prepare("SELECT * FROM bronnen WHERE punt_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
while ($bron = $result->fetch_assoc()) {
    $bronnen[] = $bron;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $x_coordinaat = $_POST['x_coordinaat'];
    $y_coordinaat = $_POST['y_coordinaat'];
    $hoogte = $_POST['hoogte'];
    $breedte = $_POST['breedte'];

    // Update punten
    $stmt = $conn->prepare("UPDATE punten SET x_coordinaat=?, y_coordinaat=?, hoogte=?, breedte=? WHERE id=?");
    $stmt->bind_param("ssssi", $x_coordinaat, $y_coordinaat, $hoogte, $breedte, $id);
    $stmt->execute();

    // Update bronnen
    if (isset($_POST['bronnen']) && is_array($_POST['bronnen'])) {
        foreach ($_POST['bronnen'] as $bron_id => $bron_data) {
            if (isset($bron_data['referentie_tekst'])) {
                $referentie_tekst = $bron_data['referentie_tekst'];
                $stmt = $conn->prepare("UPDATE bronnen SET referentie_tekst=? WHERE id=?");
                $stmt->bind_param("si", $referentie_tekst, $bron_id);
                $stmt->execute();
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

<h1>Bewerk Punt</h1>
<form method="post" class="mb-3">
    <div class="mb-3">
        <label for="x_coordinaat" class="form-label">x_coordinaat:</label>
        <input type="text" class="form-control" id="x_coordinaat" name="x_coordinaat" value="<?= htmlspecialchars($row['x_coordinaat']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="y_coordinaat" class="form-label">y_coordinaat:</label>
        <input type="text" class="form-control" id="y_coordinaat" name="y_coordinaat" value="<?= htmlspecialchars($row['y_coordinaat']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="hoogte" class="form-label">hoogte:</label>
        <input type="text" class="form-control" id="hoogte" name="hoogte" value="<?= htmlspecialchars($row['hoogte']) ?>" required>
    </div>

    <div class="mb-3">
        <label for="breedte" class="form-label">breedte:</label>
        <input type="text" class="form-control" id="breedte" name="breedte" value="<?= htmlspecialchars($row['breedte']) ?>" required>
    </div>

    <?php foreach ($bronnen as $bron): ?>
    <div class="mb-3">
        <label for="bronnen[<?= $bron['id'] ?>][referentie_tekst]" class="form-label">Referentie tekst voor bron: <?= htmlspecialchars($bron['titel']) ?>:</label>
        <input type="text" class="form-control" id="bronnen[<?= $bron['id'] ?>][referentie_tekst]" name="bronnen[<?= $bron['id'] ?>][referentie_tekst]" value="<?= htmlspecialchars($bron['referentie_tekst']) ?>">
    </div>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary">Opslaan</button>
</form>
<a href="admin.php" class="btn btn-secondary">Terug</a>

<?php include "assets/includes/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


</body>
</html>