<?php
// verwijdeerd.php
session_start();
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
    header('Location: inlog.php');
    exit();
}

include 'assets/includes/connectie.php';

// Verwijderde punten (van ALLE panorama's)
$sql_punten = "SELECT p.*, pan.titel as panorama_titel 
               FROM punten p 
               LEFT JOIN panorama pan ON p.panorama_id = pan.id 
               WHERE p.deleted_at IS NOT NULL 
               ORDER BY p.deleted_at DESC";
$result_punten = $conn->query($sql_punten);

// Verwijderde bronnen (van ALLE punten)
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
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 0;
            margin-bottom: 30px;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 2.2rem;
            font-weight: 700;
        }

        .back-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 50px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        .section {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            color: #4a5568;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 15px;
            margin-bottom: 20px;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: #667eea;
        }

        .table-container {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .data-table th {
            background: #f7fafc;
            color: #4a5568;
            font-weight: 600;
            text-align: left;
            padding: 15px;
            border-bottom: 2px solid #e2e8f0;
        }

        .data-table td {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: top;
        }

        .data-table tr:hover {
            background-color: #f8fafc;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            border: none;
            cursor: pointer;
        }

        .btn-restore {
            background-color: #48bb78;
            color: white;
        }

        .btn-restore:hover {
            background-color: #38a169;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(72, 187, 120, 0.3);
        }

        .btn-delete {
            background-color: #f56565;
            color: white;
        }

        .btn-delete:hover {
            background-color: #e53e3e;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(245, 101, 101, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #a0aec0;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            color: #cbd5e0;
        }

        .empty-state h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #718096;
        }

        .item-id {
            color: #718096;
            font-family: monospace;
            background: #f7fafc;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .item-title {
            font-weight: 500;
            color: #2d3748;
            margin-bottom: 5px;
        }

        .item-meta {
            color: #718096;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .deleted-date {
            color: #e53e3e;
            font-weight: 500;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background-color: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #48bb78;
        }

        .alert-error {
            background-color: #fed7d7;
            color: #742a2a;
            border-left: 4px solid #f56565;
        }

        .alert i {
            font-size: 1.2rem;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header .container {
                flex-direction: column;
                text-align: center;
                gap: 15px;
            }

            .page-title {
                font-size: 1.8rem;
            }

            .section {
                padding: 20px;
            }

            .data-table th,
            .data-table td {
                padding: 10px;
                font-size: 0.9rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 8px;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
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
        // Toon success/error messages
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
                                            <a href="herstel.php?type=punt&id=<?= $row['id'] ?>&panorama_id=<?= $row['panorama_id'] ?>"
                                                class="btn btn-restore" onclick="return confirm('Punt herstellen?')">
                                                <i class="fas fa-undo"></i> Herstel
                                            </a>
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
                                            <a href="herstel.php?type=bron&id=<?= $row['id'] ?>&punt_id=<?= $row['punt_id'] ?>"
                                                class="btn btn-restore" onclick="return confirm('Bron herstellen?')">
                                                <i class="fas fa-undo"></i> Herstel
                                            </a>
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
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Geen verwijderde bronnen</h3>
                    <p>Er zijn momenteel geen bronnen in de prullenbak.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="section" style="background: #f7fafc; text-align: center;">
            <p style="color: #718096; margin-bottom: 10px;">
                <i class="fas fa-info-circle"></i>
                Verwijderde items blijven 30 dagen bewaard voordat ze automatisch worden opgeruimd.
            </p>
            <p style="color: #a0aec0; font-size: 0.9rem;">
                Totaal verwijderde items:
                <strong><?= ($result_punten ? $result_punten->num_rows : 0) + ($result_bronnen ? $result_bronnen->num_rows : 0) ?></strong>
            </p>
        </div>
    </div>

    <?php include "assets/includes/footer.php"; ?>
</body>

</html>