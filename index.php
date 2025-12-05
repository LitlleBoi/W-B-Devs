<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <script src="assets/js/side-bar.js" defer></script>
    <script src="assets/js/pop-up.js" defer></script>
    <script src="assets/js/omschrijving.js" defer></script>
    <script src="assets/js/recoords.js" defer></script>
</head>
<?php
include 'assets/includes/connectie.php';
?>

<body>



    <main>
        <div id="layout" class="grid menu-hidden-desktop">
            <!-- Open Button - Shows when sidebar is closed -->
            <button id="openMenu" class="toggle-btn open-btn">
                ☰
            </button>

            <!-- Sidebar - Overlay drawer -->
            <aside id="menu" class="menu">
                <!-- Close Button - Inside sidebar -->
                <button id="closeMenu" class="toggle-btn close-btn">
                    ×
                </button>
                <div id="info">
                    <div id="info"></div>
            </aside>
            <div class="content">
                <?php foreach ($info as $panorama): ?>
                    <div class="panorama">
                        <h2><?php echo $panorama['titel']; ?></h2>

                        <img src="<?php echo $panorama['afbeelding']; ?>" alt="<?php echo $panorama['titel']; ?>">
                        <h2><?php echo $panorama['pagina']; ?></h2>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($punten as $punt): ?>
                    <!-- KNOP OP DE KAART -->
                    <button data-modal-target="#modal-<?php echo $punt['id']; ?>" class="punt" style="top:<?php echo $punt['y']; ?>px; left:<?php echo $punt['x']; ?>px;
                    height:<?php echo $punt['hoogte']; ?>px; width:<?php echo $punt['breedte']; ?>px;"
                        alt="<?php echo $punt['titel']; ?>">
                    </button>

                    <!-- POP-UP MODAL -->
                    <div class="modal" id="modal-<?php echo $punt['id']; ?>">
                        <div class="modal-header">
                            <div class="titel"><?php echo $punt['titel']; ?></div>
                            <button data-close-button class="close-button">&times;</button>
                        </div>
                        <div class="modal-body">
                            <!-- PUNT BESCHRIJVING -->
                            <div class="punt-beschrijving">
                                <?php echo $punt['omschrijving']; ?>
                            </div>

                            <!-- BRONNEN SECTIE -->
                            <?php
                            // Zoek bronnen voor dit punt
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


        </div>
    </main>

    <script>
        document.addEventListener('click', function (event) {
            let container = document.querySelector('main');
            const rect = container.getBoundingClientRect();

            // Position relative to the main container
            const xRelativeToContainer = event.clientX - rect.left;
            const yRelativeToContainer = event.clientY - rect.top;

            console.log('X', xRelativeToContainer.toFixed(0), 'Y', yRelativeToContainer.toFixed(2));
        });
    </script>

</body>

</html>