<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Success</title>
</head>
<body>

<?php
    require 'conn.php';

    if (isset($_POST['login'])) {

        $username = $_POST['username'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        if ($role == 'admin') {
            $sql = "SELECT * FROM users 
            WHERE email='$username' 
            AND password_hash='$password' 
            AND role='admin'";
        } 
        else {
            $sql = "SELECT * FROM users 
            WHERE email='$username' 
            AND password_hash='$password' 
            AND role='user'";
        }   








    }


?>
    
</body>
</html>