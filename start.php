<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/start.css">
    <title>Panorama Explorer - Welcome</title>

</head>

<body>
    <?php include 'assets/includes/header.php'; ?>
    <div class="container-start">
        <h1>Panorama verkenner</h1>


        <img src="assets/img/start.png" alt="Panorama Preview" class="preview-image">

        <div class="instructions">
            <div class="instruction-item">
                <div class="icon">🎯</div>
                <div class="instruction-content">
                    <h3>Hoe te navigeren</h3>
                    <p>Navigeren Scroll naar links en rechts om het panorama te verkennen. Klik op hotspots voor
                        informatie. Gebruik de minikaart onderaan om snel naar verschillende secties te springen.</p>
                </div>
            </div>
        </div>

        <div class="button-container">
            <a href="panorama.php" class="btn btn-primary">Panorama</a>
        </div>
    </div>
    <?php include 'assets/includes/footer.php'; ?>
    <script>
        // No scripts needed for simple link navigation
    </script>
</body>

</html>