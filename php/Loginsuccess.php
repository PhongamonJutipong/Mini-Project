<?php
session_start();
require 'conn.php';

if (isset($_POST['login'])) {
    $email = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM user WHERE user_email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
      
        if (password_verify($password, $user['user_password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_picture'] = $user['user_picture'];
            header("Location: main.php");
            exit();
        } 

        elseif ($password === $user['user_password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_picture'] = $user['user_picture'];

            header("Location: main.php");
            exit();
        } 
        else {
            $_SESSION['login_error'] = "รหัสผ่านไม่ถูกต้อง";
            header("Location: Login.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "ไม่พบบัญชีผู้ใช้";
        header("Location: Login.php");
        exit();
    }
}
?>
