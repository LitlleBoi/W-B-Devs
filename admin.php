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
session_start();
// simple access control: only allow when logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
	header('Location: inlog.php');
	exit();
}

?>

<?php
include 'assets/includes/connectie.php';
$sql = "SELECT * FROM panorama ORDER BY aangemaakt_op DESC";
$result = $conn->query($sql);
?>

	<?php include "assets/includes/header.php"; ?>

    <h1>panorama</h1>

	<div class="tabel">
    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>ID</th>
            <th>Naam</th>
            <th>catalogusnummer</th>
            <th>Afbeelding</th>
            <th>Acties</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['titel']) ?></td>
            <td><?= htmlspecialchars($row['catalogusnummer']) ?></td>
            <td>
                <?php if (!empty($row['afbeelding_url'])): ?>
                    <img src="<?= htmlspecialchars($row['afbeelding_url']) ?>" width="80">
                <?php endif; ?>
            </td>
            <td>
				<div class="adminlink">
                <a href="bewerk.php?id=<?= $row['id'] ?>">Bewerk</a> 
				</div>
			</td>
        </tr>
        <?php endwhile; ?>
    </table>
	</div>
	<?php include "assets/includes/footer.php"; ?>



</body>
</html>

