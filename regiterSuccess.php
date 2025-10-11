<?php
session_start();
require 'conn.php'; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $role = "user"; 

    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $_SESSION['error'] = "Email already exists. Please use a different email.";
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (username, email, phone, password, role) VALUES ('$name', '$email', '$phone', '$password', '$role')");
    }

    header("Location: Login.php");
    exit();
}
?>