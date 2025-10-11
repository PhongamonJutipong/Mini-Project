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

        $username = trim($_POST['username']);
        $password = $_POST['password'];
        $role = $_POST['role'];

        //chatgpt เพิ่มความปลอดภัย(sql injection)
        // code นี้จะใช้ได้ก็ต่อเมื่อ password เป็น password_hash เท่านั้น

        $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND role=? AND status='active'");
        $stmt->bind_param("ss", $username, $role);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if($user && password_verify($password, $user['password_hash'])){
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['display_name'];
        $_SESSION['role'] = $user['role'];

        if ($role == "admin") {
            header("Location: admin_mainpage.php"); // อย่าลืมทำหน้า admin_mainpage.php
            exit();

        } else if ($role == "user") {
            header("Location: main.html"); // ไปหน้า home
            exit();

        }
        } else {
            echo "<script>alert('username or password incorrect!'); window.location='login.html';</script>";
        }
    }
?>
    
</body>
</html>