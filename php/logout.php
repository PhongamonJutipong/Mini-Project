<?php
session_start();

// ลบข้อมูล session ทั้งหมด
$_SESSION = array();

// ทำลาย session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// ทำลาย session
session_destroy();

// Redirect ไปหน้า login หรือ index
header("Location: login.php"); // เปลี่ยนเป็นหน้าที่ต้องการ
exit;
?>