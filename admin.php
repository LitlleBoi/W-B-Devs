<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Document</title>
    <link rel="stylesheet" href="assets/css/includes/header.css" />
    <link rel="stylesheet" href="assets/css/includes/style.css" />
    <link rel="stylesheet" href="assets/css/includes/footer.css" />
</head>

<body>
    <?php
    include "assets/includes/header.php";
    include 'assets/includes/connectie.php';
    include 'assets/includes/footer.php';  
    ?>

<?php
// simple access control: only allow when logged in
if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'true') {
	header('Location: inlog.php');
	exit();
}

?>


