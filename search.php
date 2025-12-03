<?php
session_start();

include 'connect.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

$results = [];
if (!empty($query)) {
    $stmt = $conn->prepare("SELECT * FROM panorama WHERE name LIKE ? OR titel LIKE ? OR id");
    $searchTerm = "%$query%";
    $stmt->bind_param("sssssssss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    $results = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Zoekresultaten</title>
    <link rel="stylesheet" href="index.css">
</head>
<body>

<?php include "assets/includes/header.php"; ?>

<h1>Zoekresultaten voor: "<?= htmlspecialchars($query) ?>"</h1>

<?php if (!empty($results)): ?>
    <div class="wanted-container">
    <?php foreach ($results as $row): ?>
        <div class="wanted">
            <img class="poster" src="image/wanted.png" alt="Wanted Poster">
            <div class="content">
                <?php if (!empty($row['img'])): ?>
                    <img class="character" src="uploads/<?= htmlspecialchars($row['img']) ?>" alt="<?= htmlspecialchars($row['name']) ?>">
                <?php endif; ?>
                <h2><?= htmlspecialchars($row['name']) ?></h2>
                <p>💰 <?= htmlspecialchars($row['bounty']) ?></p>
                <a href="view.php?id=<?= $row['product_id'] ?>">Bekijk</a>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>Geen resultaten gevonden voor "<?= htmlspecialchars($query) ?>".</p>
<?php endif; ?>


<?php include "./footer.php"; ?>

<button id="scrollToTopBtn" style="display: none; position: fixed; bottom: 20px; right: 20px; z-index: 99; border: none; outline: none; background-color: #5739ffff; color: white; cursor: pointer; padding: 15px; border-radius: 10px; font-size: 18px;">↑</button>

<script src="script.js"></script>

</body>
</html>
