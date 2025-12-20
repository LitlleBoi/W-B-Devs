<?php
/**
 * Admin Dashboard Pagina
 *
 * Deze pagina biedt een administratieve interface voor het beheren van panorama's.
 * Het bevat functionaliteit om panorama-items in de database te bekijken, bewerken en beheren.
 * Toegang is beperkt tot ingelogde gebruikers alleen.
 */

// Inclusief benodigde bestanden en start sessie
session_start();

// Eenvoudige toegangscontrole: alleen toestaan wanneer ingelogd
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
    header('Location: inlog.php');
    exit();
}

include 'assets/includes/connectie.php';

// Controleer databaseverbinding
if (!$conn) {
    die("Databaseverbinding mislukt");
}

// Haal panorama's op uit database
$sql = "SELECT * FROM panorama ORDER BY aangemaakt_op DESC";
$result = $conn->query($sql);

// Controleer of query succesvol was
if ($result === false) {
    die("Query mislukt: " . $conn->error);
}

// Verkrijg totaal aantal panorama's
$total_panoramas = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/includes/header.css" />
    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/includes/footer.css" />
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="admin-page">
    <?php include "assets/includes/header.php"; ?>

    <div class="admin-container">
        <h1 class="section-title"><i class="fas fa-images"></i> Panorama Beheer</h1>

        <!-- Dashboard Stats -->
        <div class="dashboard-stats">

        </div>

        <!-- Panorama's Tabel -->
        <div class="admin-section">
            <div class="section-header">
                <h2 class="section-title"><i class="fas fa-list"></i> Alle Panorama's</h2>

            </div>

            <?php if ($total_panoramas > 0): ?>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Naam</th>
                                <th>Catalogusnummer</th>
                                <th>Afbeelding</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>#<?= $row['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($row['titel']) ?></strong>
                                        <?php if (!empty($row['beschrijving'])): ?>
                                            <p class="text-muted" style="font-size: 0.9rem; margin-top: 5px;">
                                                <?= substr(htmlspecialchars($row['beschrijving']), 0, 80) ?>...
                                            </p>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['catalogusnummer'] ?? 'N/A') ?></td>
                                    <td>
                                        <?php if (!empty($row['afbeelding_url'])): ?>
                                            <img src="<?= htmlspecialchars($row['afbeelding_url']) ?>" width="80" height="60"
                                                style="border-radius: 4px; object-fit: cover;"
                                                alt="<?= htmlspecialchars($row['titel']) ?>">
                                        <?php else: ?>
                                            <span style="color: #999; font-size: 0.9rem;">
                                                <i class="fas fa-image"></i> Geen afbeelding
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="bewerk.php?id=<?= $row['id'] ?>" class="action-btn edit" title="Bewerk">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="verwijdeerd.php" class="action-btn restore" title="Verwijderde items">
                                                <i class="fas fa-trash-restore"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Geen panorama's gevonden</h3>
                    <p>Er zijn nog geen panorama's toegevoegd aan het systeem.</p>
                    <a href="toevoegen.php" class="btn btn-primary mt-2">
                        <i class="fas fa-plus"></i> Voeg je eerste panorama toe
                    </a>
                </div>
            <?php endif; ?>
        </div>



        <?php include "assets/includes/footer.php"; ?>

</body>

</html>