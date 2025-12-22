<?php
/**
 * Verwijderde Items Pagina
 *
 * Deze pagina toont alle verwijderde items (punten en bronnen) die nog niet definitief zijn verwijderd.
 * Gebruikers kunnen items herstellen of definitief verwijderen. Alleen ingelogde gebruikers hebben toegang.
 */

// Start sessie voor gebruikersbeheer
session_start();

// Controleer of gebruiker is ingelogd
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
    // Stuur niet-ingelogde gebruikers naar de inlogpagina
    header('Location: inlog.php');
    exit();
}

// Inclusie van database connectie
include 'assets/includes/connectie.php';

// Haal verwijderde punten op van alle panorama's, inclusief panorama titel
$sql_punten = "SELECT p.*, pan.titel as panorama_titel
               FROM punten p
               LEFT JOIN panorama pan ON p.panorama_id = pan.id
               WHERE p.deleted_at IS NOT NULL
               ORDER BY p.deleted_at DESC";
$result_punten = $conn->query($sql_punten);

// Haal verwijderde bronnen op van alle punten, inclusief punt en panorama titels
$sql_bronnen = "SELECT b.*, p.titel as punt_titel, pan.titel as panorama_titel
                FROM bronnen b
                LEFT JOIN punten p ON b.punt_id = p.id
                LEFT JOIN panorama pan ON p.panorama_id = pan.id
                WHERE b.deleted_at IS NOT NULL
                ORDER BY b.deleted_at DESC";
$result_bronnen = $conn->query($sql_bronnen);
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verwijderde Items</title>
    <!-- Inclusie van algemene CSS stijlen -->
    <!-- <link rel="stylesheet" href="assets/css/style.css"> -->
    <link rel="stylesheet" href="assets/css/verwijdeerd.css">
    <!-- Inclusie van Font Awesome iconen -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Inline CSS stijlen voor de pagina layout en componenten -->

</head>

<body>
    <div class="header">
        <div class="container">
            <h1 class="page-title"><i class="fas fa-trash-restore"></i> Verwijderde Items</h1>
            <a href="admin.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> Terug naar Beheer
            </a>
        </div>
    </div>

    <div class="container">
        <?php
        // Toon success/error messages die zijn opgeslagen in de sessie
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>

        <!-- Verwijderde Punten Sectie -->
        <div class="section">
            <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Verwijderde Punten</h2>

            <?php if ($result_punten && $result_punten->num_rows > 0): ?>
                <!-- Als er verwijderde punten zijn, toon tabel -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titel</th>
                                <th>Panorama</th>
                                <th>Verwijderd op</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_punten->fetch_assoc()): ?>
                                <tr>
                                    <td><span class="item-id">#<?= $row['id'] ?></span></td>
                                    <td>
                                        <div class="item-title"><?= htmlspecialchars($row['titel']) ?></div>
                                        <?php if (!empty($row['omschrijving'])): ?>
                                            <!-- Toon verkorte omschrijving als die er is -->
                                            <div class="item-meta"><?= substr(htmlspecialchars($row['omschrijving']), 0, 80) ?>...
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="item-title"><?= htmlspecialchars($row['panorama_titel']) ?></div>
                                        <div class="item-meta">Panorama ID: <?= $row['panorama_id'] ?></div>
                                    </td>
                                    <td>
                                        <span class="deleted-date">
                                            <i class="far fa-clock"></i> <?= date('d-m-Y H:i', strtotime($row['deleted_at'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <!-- Herstel knop met confirm dialoog -->
                                            <a href="herstel.php?type=punt&id=<?= $row['id'] ?>&panorama_id=<?= $row['panorama_id'] ?>"
                                                class="btn btn-restore" onclick="return confirm('Punt herstellen?')">
                                                <i class="fas fa-undo"></i> Herstel
                                            </a>
                                            <!-- Definitief verwijder knop met waarschuwing -->
                                            <a href="verwijder_definitief.php?type=punt&id=<?= $row['id'] ?>"
                                                class="btn btn-delete"
                                                onclick="return confirm('Punt definitief verwijderen? Dit kan niet ongedaan gemaakt worden!')">
                                                <i class="fas fa-trash"></i> Definitief
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- Toon lege staat als er geen verwijderde punten zijn -->
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Geen verwijderde punten</h3>
                    <p>Er zijn momenteel geen punten in de prullenbak.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Verwijderde Bronnen Sectie -->
        <div class="section">
            <h2 class="section-title"><i class="fas fa-book"></i> Verwijderde Bronnen</h2>

            <?php if ($result_bronnen && $result_bronnen->num_rows > 0): ?>
                <!-- Als er verwijderde bronnen zijn, toon tabel -->
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Titel</th>
                                <th>Punt</th>
                                <th>Panorama</th>
                                <th>Verwijderd op</th>
                                <th>Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_bronnen->fetch_assoc()): ?>
                                <tr>
                                    <td><span class="item-id">#<?= $row['id'] ?></span></td>
                                    <td>
                                        <div class="item-title"><?= htmlspecialchars($row['titel']) ?></div>
                                        <?php if (!empty($row['auteur'])): ?>
                                            <!-- Toon auteur als die er is -->
                                            <div class="item-meta">Auteur: <?= htmlspecialchars($row['auteur']) ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="item-title"><?= htmlspecialchars($row['punt_titel']) ?></div>
                                        <div class="item-meta">Punt ID: <?= $row['punt_id'] ?></div>
                                    </td>
                                    <td>
                                        <div class="item-title"><?= htmlspecialchars($row['panorama_titel']) ?></div>
                                    </td>
                                    <td>
                                        <span class="deleted-date">
                                            <i class="far fa-clock"></i> <?= date('d-m-Y H:i', strtotime($row['deleted_at'])) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <!-- Herstel knop voor bron -->
                                            <a href="herstel.php?type=bron&id=<?= $row['id'] ?>&punt_id=<?= $row['punt_id'] ?>"
                                                class="btn btn-restore" onclick="return confirm('Bron herstellen?')">
                                                <i class="fas fa-undo"></i> Herstel
                                            </a>
                                            <!-- Definitief verwijder knop voor bron -->
                                            <a href="verwijder_definitief.php?type=bron&id=<?= $row['id'] ?>"
                                                class="btn btn-delete"
                                                onclick="return confirm('Bron definitief verwijderen? Dit kan niet ongedaan gemaakt worden!')">
                                                <i class="fas fa-trash"></i> Definitief
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- Toon lege staat als er geen verwijderde bronnen zijn -->
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Geen verwijderde bronnen</h3>
                    <p>Er zijn momenteel geen bronnen in de prullenbak.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Informatie sectie met footer info -->
        <div class="section" style="background: #f7fafc; text-align: center;">
            <p style="color: #718096; margin-bottom: 10px;">
                <i class="fas fa-info-circle"></i>
                Verwijderde items blijven 30 dagen bewaard voordat ze automatisch worden opgeruimd.
            </p>
            <p style="color: #a0aec0; font-size: 0.9rem;">
                <!-- Toon totaal aantal verwijderde items -->
                Totaal verwijderde items:
                <strong><?= ($result_punten ? $result_punten->num_rows : 0) + ($result_bronnen ? $result_bronnen->num_rows : 0) ?></strong>
            </p>
        </div>
    </div>

    <!-- Inclusie van footer -->
    <?php include "assets/includes/footer.php"; ?>
</body>

</html>