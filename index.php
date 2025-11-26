<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/pop-up.css">
    <script src="assets/js/pop-up.js" defer></script>
    <script src="assets/js/x-y.js" defer></script>
</head>
<?php
include 'assets/includes/connectie.php';

?>


<body>

    <main>
        <?php foreach ($info as $panorama): ?>
            <div class="panorama">
                <h2><?php echo $panorama['titel']; ?></h2>
                <img src="<?php echo $panorama['afbeelding']; ?>" alt="<?php echo $panorama['titel']; ?>">

            </div>
        <?php endforeach; ?>

        <?php foreach ($punten as $punt): ?>
            <button data-modal-target="#modal-<?php echo $punt['id']; ?>" class="punt" style="top:<?php echo $punt['y']; ?>px; left:<?php echo $punt['x']; ?>px;
                height:<?php echo $punt['hoogte']; ?>px; width:<?php echo $punt['breedte']; ?>px;"
                alt="<?php echo $punt['titel']; ?>">
            </button>
            <div class="modal" id="modal-<?php echo $punt['id']; ?>">
                <div class="modal-header">
                    <div class="titel"><?php echo $punt['titel']; ?></div>
                    <button data-close-button class="close-button">&times;</button>
                </div>
                <div class="modal-body">
                    <?php echo $punt['omschrijving']; ?>
                </div>
            </div>
            <?php
            $gevonden_bronnen = [];
            foreach ($bronnen as $bron) {
                if ($bron['punt_id'] == $punt['id']) {
                    $gevonden_bronnen[] = $bron;
                }

            }
            ?>
            <?php foreach ($gevonden_bronnen as $bron): ?>
                <div class="bron-item">
                    <h4><?php echo $bron['titel']; ?></h4>

                    <?php if (!empty($bron['auteur'])): ?>

                        <p><strong>Auteur:</strong> <?php echo $bron['auteur']; ?></p>
                    <?php endif; ?>

                    <?php if (!empty($bron['referentie_tekst'])): ?>

                        <p><?php echo $bron['referentie_tekst']; ?></p>
                    <?php endif; ?>

                    <?php if (!empty($bron['afbeelding'])): ?>
                        <img src="<?php echo $bron['afbeelding']; ?>" alt="<?php echo $bron['titel']; ?>">

                    <?php endif; ?>

                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <div id="overlay"></div>
    </main>
    <script>

        document.addEventListener('click', function (event) {
            let container = document.querySelector('main');
            console.log('X:', event.target.offsetLeft, 'Y:', event.clientY);
        });
    </script>
    <?php include 'assets/includes/x-y.php'; ?>
</body>

</html>