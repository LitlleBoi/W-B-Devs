<?php
/**
 * Inlog Pagina
 *
 * Deze pagina verwerkt gebruikersauthenticatie door email en wachtwoord te controleren
 * tegen de database. Bij succesvolle login wordt de gebruiker doorgestuurd naar admin.php.
 */

// Start sessie voor gebruikersbeheer
session_start();

// Inclusie van database connectie
include 'assets/includes/connectie.php';

// Controleer of login formulier is ingediend
if ((isset($_GET["email"])) && (isset($_GET["wachtwoord"]))) {
    // Haal email en wachtwoord op uit GET parameters
    $email = $_GET["email"];
    $password = $_GET["wachtwoord"];

    // Bereid SQL query voor om gebruiker te vinden
    $stmt = $conn->prepare("SELECT * FROM gebruikers WHERE email = ? AND `wachtwoord` = ?");
    if ($stmt) {
        // Bind parameters aan de query
        $stmt->bind_param("ss", $email, $password);
        // Voer query uit
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            // Gebruiker gevonden - login succesvol
            $_SESSION["login"] = "true";
            // Redirect naar admin pagina
            header('Location: admin.php');
            exit();
        }
        $stmt->close();
    } else {
        // Query voorbereiding mislukt - handel af indien nodig
    }
}



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">


    <link rel="stylesheet" href="assets/css/style.css" />
    <link rel="stylesheet" href="assets/css/inlog.css" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .row {
            background: red;
            display: block;
            height: 30px;
            border: 1px solid black;
        }
    </style>
</head>

<body>
    <?php include 'assets/includes/header.php'; ?>

    <?php


    $conn->close();

    ?>

    <div class="inlogbackground">
        <div class="inlogtekst">
            <form method="GET" action="inlog.php">
                <div class="email">
                    <text>E:mail:</text>
                    <input type="text" name="email" required placeholder="Email">
                </div>
                <div class="wachtwoord">
                    <text>Wachtwoord:</text>
                    <input type="wachtwoord" name="wachtwoord" required placeholder="Wachtwoord">
                </div>
                <input type="submit" value="Versturen">
            </form>
        </div>
    </div>

    <?php include 'assets/includes/footer.php';
    ?>
</body>

</html>