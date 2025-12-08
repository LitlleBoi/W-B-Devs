<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panorama</title>
    <link rel="stylesheet" href="assets/css/style.css">

    <!-- Load scripts in correct order -->
    <script src="assets/js/side-bar.js" defer></script>
    <script src="assets/js/pop-up.js" defer></script>
    <script src="assets/js/omschrijving.js" defer></script>
    <script src="assets/js/recoords.js" defer></script>
    <script src="assets/js/script.js" defer></script>
</head>

<?php
include 'assets/includes/select.php';
?>

<body class="no-vertical-scroll">
    <?php include 'assets/includes/header.php'; ?>

    <!-- Sidebar toggle -->
    <button class="toggle-btn open-btn" id="openMenu">
        <span class="menu-icon">☰</span>
    </button>

    <!-- Sidebar - starts open -->
    <div class="menu active">
        <button class="toggle-btn close-btn" id="closeMenu">
            <span class="menu-icon">✕</span>
        </button>
        <div id="info"></div>
    </div>

    <!-- Main panorama container -->
    <main class="panorama-container" id="panoramaFotos">
        <div class="panorama-strip">
            <?php foreach ($info as $panorama): ?>
                <div class="panorama" data-panorama-id="<?php echo $panorama['id']; ?>">
                    <img src="<?php echo $panorama['afbeelding']; ?>" alt="<?php echo $panorama['titel']; ?>">

                    <?php foreach ($punten as $punt): ?>
                        <?php if ($punt['panorama_id'] == $panorama['id']): ?>
                            <button data-modal-target="#modal-<?php echo $punt['id']; ?>" class="punt"
                                data-x="<?php echo $punt['x']; ?>" data-y="<?php echo $punt['y']; ?>"
                                data-panorama-id="<?php echo $punt['panorama_id']; ?>" data-punt-id="<?php echo $punt['id']; ?>"
                                title="<?php echo htmlspecialchars($punt['titel']); ?>">
                                <span class="punt-dot"></span>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div id="overlay" class="overlay"></div>

    </main>

    <!-- Minimap -->
    <div id="panoramaMinimap" class="panorama-minimap">
        <div id="panoramaMinimapViewport" class="panorama-minimap-viewport"></div>
    </div>

    <!-- Modals for points -->
    <?php foreach ($punten as $punt): ?>
        <div class="modal" id="modal-<?php echo $punt['id']; ?>">
            <div class="modal-header">
                <div class="title"><?php echo htmlspecialchars($punt['titel']); ?></div>
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
                                    <h4><?php echo htmlspecialchars($bron['titel']); ?></h4>

                                    <?php if (!empty($bron['auteur'])): ?>
                                        <p><strong>Auteur:</strong> <?php echo htmlspecialchars($bron['auteur']); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($bron['referentie_tekst'])): ?>
                                        <p><?php echo htmlspecialchars($bron['referentie_tekst']); ?></p>
                                    <?php endif; ?>

                                    <?php if (!empty($bron['afbeelding'])): ?>
                                        <img src="<?php echo htmlspecialchars($bron['afbeelding']); ?>"
                                            alt="<?php echo htmlspecialchars($bron['titel']); ?>"
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
    <!-- DEBUG: Check data -->
    <div style="display: none;">
        <?php
        echo "Number of panoramas: " . count($info) . "<br>";
        echo "Number of points: " . count($punten) . "<br>";

        foreach ($punten as $i => $punt) {
            echo "Point {$i}: ID={$punt['id']}, ";
            echo "X={$punt['x']}, Y={$punt['y']}, ";
            echo "Panorama={$punt['panorama_id']}<br>";
        }
        ?>
    </div>

    <?php include 'assets/includes/footer.php'; ?>
</body>

</html>