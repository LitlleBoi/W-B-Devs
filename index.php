<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<?php
include 'assets/includes/connectie.php'; ?>

<body>

    <?php foreach ($panorama as $item): ?>
        <h2><?php echo $item['titel']; ?></h2>
        <img src="<?php echo $item['afbeelding']; ?>" alt="<?php echo $item['titel']; ?>">
        <p><?php echo $item['beschrijving']; ?></p>
    <?php endforeach; ?>
</body>

</html>