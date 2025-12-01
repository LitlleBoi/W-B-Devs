<?php
session_start();
include 'assets/includes/connectie.php';
include 'assets/includes/header.php';

if ((isset($_GET["email"]))&&(isset($_GET["wachtwoord"])))
{
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
            $_SESSION["login"] = "true";{
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
    <link rel="stylesheet" href="assets/css/includes/header.css" />
    <link rel="stylesheet" href="assets/css/includes/start.css" />
    <link rel="stylesheet" href="assets/css/includes/style.css" />
    <link rel="stylesheet" href="assets/css/includes/inlog.css" />
    <link rel="stylesheet" href="assets/css/includes/footer.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .row{
            background:red;
            display:block;
            height:30px;
            border:1px solid black;
        }
        </style>
</head>
<body>

<!-- <br>

<?php


    $stmt = $conn->prepare("SELECT * FROM gebruikers");
    $stmt->execute();
    $res = $stmt->get_result();

    // print all values in $result (if any)
        if ($res) {
                while ($v = $res->fetch_assoc()) {
                        echo "<div class='row'>rol:  " . $v['rol']. " - id:  " . $v['id']. " - email: " . $v['email']. " - Wachtwoord: " . $v['wachtwoord']. "</div>";
                }
                $res->free();
        }

  

$conn = null;

?> -->

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