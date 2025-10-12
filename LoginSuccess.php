<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Success</title>
</head>
<body>

<?php
    session_start();
    require 'conn.php';

    if (isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
        if($result->num_rows >0){
            $user = $result -> fetch_assoc();

            if($user && password_verify($password, $user['password'])){           
                $_SESSION['email'] = $user['email'];
                $_SESSION['display_name'] = $user['display_name'];
                header("Location: main.html");
                exit();

            } 
        }

        $_SESSION['login_error'] = 'Incorrect email or password';
                header("location:Login.php");
                exit();
    }
?>
    
</body>
</html>