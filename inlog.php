<?php
session_start();
include 'assets/includes/connectie.php';

if ((isset($_GET["email"])) && (isset($_GET["wachtwoord"]))) {
    // prepare sql and bind parameters using mysqli
    $email = $_GET["email"];
    $password = $_GET["wachtwoord"];

    $stmt = $conn->prepare("SELECT * FROM gebruikers WHERE email = ? AND `wachtwoord` = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            // user found
            $_SESSION["login"] = "true"; {
                header('Location: admin.php');
            }



            exit();
        }
        $stmt->close();
    } else {
        // prepare failed - handle as needed
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