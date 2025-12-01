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
    include "start.php";
    include 'assets/includes/connectie.php'; 
    ?>

    <main id="content">
        


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
