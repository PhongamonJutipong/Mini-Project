<?php
$host = 'localhost';
$user = 'root';
$pass = ''; 
$dbname = 'picture_store'; 

$mysqli = new mysqli($host, $user, $pass, $dbname);

if ($mysqli->connect_errno) {
    die("Database connection failed: " . $mysqli->connect_error);
}
$mysqli->set_charset("utf8mb4");
?>
