<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/includes/header.css" />
    <link rel="stylesheet" href="assets/css/includes/style.css" />
    <link rel="stylesheet" href="assets/css/includes/footer.css" />
</head>

<body>


<?php
// simple access control: only allow when logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
	header('Location: inlog.php');
	exit();
}

?>

<?php
include 'connectie.php';
$sql = "SELECT * FROM panorama ORDER BY aangemaakt_op DESC";
$result = $conn->query($sql);
?>

	<?php include "header.php"; ?>
	<div class="nieuw">
    <a href="create.php">+ Nieuw character</a>
	</div>

    <h1>One Piece Characters</h1>

	<div class="tabel">
    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>Bounty</th>
            <th>Afbeelding</th>
            <th>Acties</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['product_id'] ?></td>
            <td><?= htmlspecialchars($row['titel']) ?></td>
            <td><?= htmlspecialchars($row['catalogsnummer']) ?></td>
            <td>
                <?php if (!empty($row['img'])): ?>
                    <img src="uploads/<?= htmlspecialchars($row['afbeelding_url']) ?>" width="80">
                <?php endif; ?>
            </td>
            <td>
				<div class="adminlink">
                <a href="bewerk.php?id=<?= $row['id'] ?>">Bewerk</a> |
                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Weet je het zeker?');">Verwijder</a>
				</div>
			</td>
        </tr>
        <?php endwhile; ?>
    </table>
	</div>
	<?php include "footer.php"; ?>



</body>
</html>

