<?php
session_start();
require 'conn.php';

if (isset($_POST['loginAdmin'])) {
    $email = trim($_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE admin_email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
      
        if (password_verify($password, $user['user_password'])) {
            $_SESSION['admin_id'] = $user['admin_id'];
            $_SESSION['admin_name'] = $user['admin_name'];
            $_SESSION['admin_email'] = $user['admin_email'];
            $_SESSION['admin_profile'] = $user['admin_profile'];
            header("Location: AdminmMain.php");
            exit();
        } 

        elseif ($password === $user['admin_password']) {
            $_SESSION['admin_id'] = $user['admin_id'];
            $_SESSION['admin_name'] = $user['admin_name'];
            $_SESSION['admin_email'] = $user['admin_email'];
            $_SESSION['admin_profile'] = $user['admin_profile'];

            header("Location: AdminmMain.php");
            exit();
        } 
        else {
            $_SESSION['login_error'] = "รหัสผ่านไม่ถูกต้อง";
            header("Location: LoginAdmin.php");
            exit();
        }
    } else {
        $_SESSION['login_error'] = "ไม่พบบัญชีผู้ใช้";
        header("Location: LoginAdmin.php");
        exit();
    }
}
?>
