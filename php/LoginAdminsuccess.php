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

    $sql = "SELECT * FROM admin WHERE admin_email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
      
        // ตรวจสอบรหัสผ่านแบบ hash (ตรวจสอบ admin_password ไม่ใช่ user_password)
        if (password_verify($password, $admin['admin_password'])) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_email'] = $admin['admin_email'];
            $_SESSION['admin_profile'] = $admin['admin_profile'];
            header("Location: AdminMain.php");
            exit();
        } 
        // ตรวจสอบรหัสผ่านแบบ plain text (สำหรับข้อมูลเก่า)
        elseif ($password === $admin['admin_password']) {
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['admin_name'] = $admin['admin_name'];
            $_SESSION['admin_email'] = $admin['admin_email'];
            $_SESSION['admin_profile'] = $admin['admin_profile'];
            header("Location: AdminMain.php");
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