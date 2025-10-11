<?php
$conn = new mysqli('localhost','root','','picture_store');
$conn->query("SET NAMES utf8");
if($conn->connect_error){
    die("Connection Fail God damn it ". $conn->connect_error);
}
?>