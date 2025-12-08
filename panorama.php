<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panorama</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- <link rel="stylesheet" href="assets/css/pop-up.css"> -->

    <script src="assets/js/side-bar.js" defer></script>
    <script src="assets/js/pop-up.js" defer></script>
    <script src="assets/js/omschrijving.js" defer></script>
    <script src="assets/js/recoords.js" defer></script>
</head>
<?php
include 'assets/includes/select.php';
?>

<body>
    <main id="panoramaFotos">
        <?php foreach ($info as $panorama): ?>
            <div class="panorama">
                <!-- <h2><?php echo $panorama['titel']; ?></h2> -->

                <img src="<?php echo $panorama['afbeelding']; ?>" alt="<?php echo $panorama['titel']; ?>">
                <!-- <h2><?php echo $panorama['pagina']; ?></h2> -->
            </div>
        <?php endforeach; ?>

        <div class="content">
            <?php foreach ($info as $panorama): ?>
                <div class="panorama" data-panorama-id="<?php echo $panorama['id']; ?>">
                    <img src="<?php echo $panorama['afbeelding']; ?>" alt="<?php echo $panorama['titel']; ?>">

                    <?php foreach ($punten as $punt): ?>
                        <?php if ($punt['panorama_id'] == $panorama['id']): ?>
                            <button data-modal-target="#modal-<?php echo $punt['id']; ?>" class="punt"
                                data-x="<?php echo $punt['x']; ?>" data-y="<?php echo $punt['y']; ?>"
                                data-panorama-id="<?php echo $punt['panorama_id']; ?>" title="<?php echo $punt['titel']; ?> ">
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>



        </div> <?php foreach ($punten as $punt): ?>
            <div class="modal" id="modal-<?php echo $punt['id']; ?>">
                <div class="modal-header">
                    <div class="titel"><?php echo $punt['titel']; ?></div>
                    <button data-close-button class="close-button">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="punt-beschrijving">
                        <?php echo $punt['omschrijving']; ?>
                    </div>

                    <?php
                    $gevonden_bronnen = [];
                    foreach ($bronnen as $bron) {
                        if ($bron['punt_id'] == $punt['id']) {
                            $gevonden_bronnen[] = $bron;
                        }
                    }
                    ?>

                    <?php if (!empty($gevonden_bronnen)): ?>
                        <div class="bronnen-sectie">
                            <h3>Bronnen:</h3>
                            <div class="bronnen-lijst">
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
                                            <img src="<?php echo $bron['afbeelding']; ?>" alt="<?php echo $bron['titel']; ?>"
                                                style="max-width: 100%; height: auto; margin-top: 10px;">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
        <div id="overlay" class="overlay"></div>
    </main>

</body>
<!-- <script>
    document.addEventListener('click', function (event) {
        // Find clicked panorama image
        const clickedElement = event.target;
        const panorama = clickedElement.closest('.panorama');

        if (panorama) {
            const img = panorama.querySelector('img');
            const imgRect = img.getBoundingClientRect();

            // Calculate position relative to IMAGE, not container
            const xRelativeToImage = event.clientX - imgRect.left;
            const yRelativeToImage = event.clientY - imgRect.top;

            console.log('CLICK ON IMAGE:');
            console.log('  Image:', img.src);
            console.log('  Image position:', imgRect.left, imgRect.top);
            console.log('  Image size:', img.offsetWidth, 'x', img.offsetHeight);
            console.log('  Click on image at:', xRelativeToImage.toFixed(0), 'X', yRelativeToImage.toFixed(0));

            // Show visual feedback
            const marker = document.createElement('div');
            marker.style.position = 'absolute';
            marker.style.left = (xRelativeToImage - 10) + 'px';
            marker.style.top = (yRelativeToImage - 10) + 'px';
            marker.style.width = '20px';
            marker.style.height = '20px';
            marker.style.backgroundColor = 'red';
            marker.style.border = '2px solid yellow';
            marker.style.borderRadius = '50%';
            marker.style.zIndex = '9999';
            panorama.appendChild(marker);

            setTimeout(() => marker.remove(), 3000);
        }
    });
</script> -->
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Document</title>

<link rel="stylesheet" href="assets/css/style.css">

</head>
<?php
include 'assets/includes/header.php';

include 'assets/includes/footer.php';
?>

<!-- #region -->
</body>


</html>