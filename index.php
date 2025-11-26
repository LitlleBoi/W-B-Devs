<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/includes/header.css" />
    <link rel="stylesheet" href="assets/css/includes/start.css" />
    <link rel="stylesheet" href="assets/css/includes/style.css" />
    <link rel="stylesheet" href="assets/css/includes/footer.css" />
</head>

<body>
    <?php
    include "assets/includes/header.php";
    include "assets/includes/start.php";
    include 'assets/includes/connectie.php'; 
    ?>

    <main id="content">
        
    <?php // Prepare and execute the SELECT query using prepared statement
$stmt = $conn->prepare("SELECT * FROM panorama");
$stmt->execute();
$result = $stmt->get_result();

$panorama = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $panorama[] = [
            'id' => $row['id'],
            'titel' => $row['titel'],
            'afbeelding_url' => $row['afbeelding_url'],
            'beschrijving' => $row['beschrijving'],
            'gebruiker_id' => $row['gebruiker_id'],
            'status' => $row['status'],
            'aangemaakt_op' => $row['aangemaakt_op'],
            'bijgewerkt_op' => $row['bijgewerkt_op']
        ];
    }
} else {
    echo "0 results";
}

$conn->close();
?>

        <?php foreach ($panorama as $item): ?>
            <h2><?php echo $item['titel']; ?></h2>
            <img src="<?php echo $item['afbeelding_url']; ?>" alt="<?php echo $item['titel']; ?>">
            <p><?php echo $item['beschrijving']; ?></p>
        <?php endforeach; ?>
     
        
    </main>

    <?php
    include "assets/includes/footer.php";
    ?>
</body>
</html>
