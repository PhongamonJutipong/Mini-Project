<?php
session_start();
require 'conn.php';

if (isset($_POST['login'])) {
    $email = trim($_POST['username']);
    $password = $_POST['password'];

    // ตรวจสอบว่ากรอกข้อมูลครบหรือไม่
    if (empty($email) || empty($password)) {
        echo "<script>
            alert('Please fill in all fields.');
            window.history.back();
        </script>";
        exit();
    }

    $sql = "SELECT * FROM user WHERE user_email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
      
        // ตรวจสอบรหัสผ่านแบบ hash
        if (password_verify($password, $user['user_password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_picture'] = $user['user_picture'];
            header("Location: main.php");
            exit();
        } 
        // ตรวจสอบรหัสผ่านแบบ plain text (สำหรับข้อมูลเก่า)
        elseif ($password === $user['user_password']) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_picture'] = $user['user_picture'];
            header("Location: main.php");
            exit();
        } 
        else {
            echo "<script>
                alert('incorrect. Please check your email or password.');
                window.history.back();
            </script>";
            exit();
        }
    } else {
        echo "<script>
            alert('incorrect. Please check your email or password.');
            window.history.back();
        </script>";
        exit();
    }
} else {
    echo "<script>
        alert('Invalid access to this page.');
        window.location.href = 'Login.php';
    </script>";
    exit();
}
?>