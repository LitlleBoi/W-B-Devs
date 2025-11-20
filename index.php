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

    <main>
        <?php foreach ($info as $panorama): ?>
            <div class="panorama">
                <h2><?php echo $panorama['titel']; ?></h2>
                <img src="<?php echo $panorama['afbeelding']; ?>" alt="<?php echo $panorama['titel']; ?>">
                <!-- <p><?php echo $panorama['beschrijving']; ?></p> -->
            </div>
        <?php endforeach; ?>
    </main>
</body>

</html>