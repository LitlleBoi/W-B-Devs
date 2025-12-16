<?php
// Detect environment
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN' || preg_match('/^192\.168\.2/', $_SERVER['SERVER_ADDR'])) {
    // Offline/local omgeving
    $servername = "localhost";  // of je offline host
    $username = "root";         // offline user
    $password = "";             // offline password
    $dbname = "w_b_devs_new_offline"; // offline database naam
} else {
    // Online/production omgeving

    $servername = "localhost";
    $username = "u240653_w_b_devs";
    $password = "fTzkCnbeDBnsj7xwrL5g"; // Default for XAMPP
    $dbname = "u240653_w_b_devs";
}

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>