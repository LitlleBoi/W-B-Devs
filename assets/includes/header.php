<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <div class="container">
        <div class="search">
            <a href="index.php">
                <img src="assets/img/search.png" alt="W&amp;B Devs search">
            </a>
        </div>
        <div class="nav">
            <a href="index.php">
                <img src="assets/img/arrow.png" alt="W&amp;B Devs search">
                <div>start</div>
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
        <div class="logo">
            <a href="index.php">
                <img src="assets/img/klein-logo.png" alt="W&amp;B Devs Logo">
            </a> 
        </div>
    </div>
</header>
