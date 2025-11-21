<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pop-up.css">
    <script src="assets/js/pop-up.js" defer></script>
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

        <?php foreach ($punten as $punt): ?>
            <div>
                <h1><?php echo $punt['titel'] ?></h1>
            </div>
        <?php endforeach; ?>
    </main>
    <button data-modal-target="#modal" class="punt<?php echo $punten[0]['id']; ?>">Click me</button>
    <div class="modal" id="modal">
        <div class="modal-header">
            <div class="titel">example modal</div>
            <button data-close-button class="close-button">&times;</button>
        </div>
        <div class="modal-body">
            Lorem ipsum dolor sit amet, consectetur adipisicing elit. Esse, culpa
            assumenda quis animi ullam excepturi ad similique nam delectus natus,
            ducimus accusamus odit laboriosam expedita perspiciatis deleniti numquam
            rerum veniam facilis maiores quo nemo? Ad perspiciatis, aut incidunt
            quasi saepe minima expedita quod deleniti libero nostrum cumque velit
            quas facilis quos vel odio voluptatem, corrupti mollitia laudantium illo
            suscipit? Alias asperiores libero iste aliquam vero sapiente corrupti
            numquam consectetur reprehenderit nostrum harum, saepe inventore. Beatae
            soluta aperiam enim qui dolorem?
        </div>
    </div>
    <div id="overlay"></div>
</body>

</html>