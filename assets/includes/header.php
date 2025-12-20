<?php
/**
 * Header Include
 *
 * Deze include bevat de HTML voor de header van de website, inclusief navigatie en logo.
 * Het start ook de sessie indien nodig en toont verschillende menu-opties gebaseerd op login status.
 */

// Start sessie als deze nog niet gestart is
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="container">
        <div class="search">
            <a href="start.php">
                <img src="assets/img/search.png" alt="W&amp;B Devs search">
            </a>
        </div>
        <div class="nav">
            <a href="start.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>Start</div>
            </a>
            <a href="panorama.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>Panorama</div>
            </a>
            <a href="https://hetutrechtsarchief.nl">
                <img src="assets/img/arrow.png" alt="W&B Devs search">
                <div>Utrechts Archief</div>
            </a>
            </a>
            <a href="inlog.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>Inlog</div>
            </a>
            <a href="admin.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>Admin</div>
            </a>
            <?php if (isset($_SESSION['login']) && $_SESSION['login'] === 'true'): ?>
                <a href="assets/includes/logout.php">
                    <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                    <div>Logout</div>
                </a>
            <?php endif; ?>
        </div>
        <div class="logo">
            <a href="start.php">
                <img src="assets/img/klein-logo.png" alt="W&amp;B Devs Logo">
            </a>
        </div>
    </div>
</header>