<?php
$db_server = 'localhost';
$db_andmebaas = 'sport';
$db_kasutaja = 'hserman';
$db_salasona = 'hserman';

$yhendus = mysqli_connect($db_server, $db_kasutaja, $db_salasona, $db_andmebaas);

if (!$yhendus) {
    die("probleem andmebaasiga!");
}
?>