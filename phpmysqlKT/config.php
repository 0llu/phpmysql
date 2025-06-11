<?php
// Andmebaasi ühenduse seaded
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'raamatukogu');

// Seansi seaded
session_set_cookie_params(14400); // 4 tundi
session_start();

// Veateadete kuvamine (arenduse ajal)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Funktsioon andmebaasi ühenduse loomiseks
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Ühenduse viga: " . $conn->connect_error);
    }
    $conn->set_charset("utf8");
    return $conn;
}

// Turvalisusfunktsioonid
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// Kasutaja autentimise funktsioonid
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
        redirect('login.php');
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        $_SESSION['error'] = "Teil pole sellele lehele juurdepääsuks õigusi";
        redirect('index.php');
    }
}
?>