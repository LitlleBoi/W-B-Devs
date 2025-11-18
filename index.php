<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <?php
    $images = glob('assets/images/**.jpg');
    foreach ($images as $image) {

        // echo "<img src='$image' alt='' style='max-width: 200px; margin: 10px;'><br>";
        echo "" . dirname($image) . "/" . basename($image) . "<br>";
        ;
    }
    $images = glob('assets/images/**/*.jpg');
    foreach ($images as $image) {

        // echo "<img src='$image' alt='' style='max-width: 200px; margin: 10px;'><br>";
        echo "" . dirname($image) . "/" . basename($image) . "<br>";
        ;
    }
    ?>
    <?php foreach (scandir('assets/images') as $item) {
        if ($item === '.' || $item === '..') continue;
        echo $item . "<br>";
    }
    
    
    ?>

</body>

</html>