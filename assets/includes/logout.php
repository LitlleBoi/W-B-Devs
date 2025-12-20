<?php
/**
 * Logout Include
 *
 * Deze include verwerkt het uitloggen van gebruikers.
 * Het vernietigt alle sessie data en cookies, en redirect naar de hoofdpagina.
 */

// Start sessie
session_start();

// Vernietig alle sessie data
$_SESSION = array();

// Vernietig sessie cookie als deze gebruikt wordt
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Vernietig de sessie
session_destroy();

// Redirect naar hoofdpagina
header('Location:index.php');
exit();
