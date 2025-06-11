<?php
require_once 'config.php';

// Kustuta sessioon
session_unset();
session_destroy();

// Kustuta "mäleta mind" küpsis
setcookie('remember_user', '', time() - 3600, '/');

// Suuna avalehele
redirect('index.php');
?>