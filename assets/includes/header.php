<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="container">
    <div id="search-bar" class="search-bar">
        <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
        <div>Panorama</div>

    </div>
        <div class="nav">
            <a href="index.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>Panorama</div>
            </a>
            <a href="index.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>Utrechts Archief</div>
            </a>
            <a href="index.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>contact</div>
            </a>
            <a href="inlog.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>inlog</div>
            </a>
            <a href="admin.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>admin</div>
            </a>
            <?php if (isset($_SESSION['login']) && $_SESSION['login'] === 'true'): ?>
            <a href="assets/includes/logout.php">
            <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
            <div>Logout</div>
            </a>
            <?php endif; ?>
        </div>
        <!-- <div class="logo">
            <a href="index.php">
                <img src="assets/img/logo.png" alt="W&amp;B Devs Logo">
            </a> -->
        </div>
    </div>
</header>
