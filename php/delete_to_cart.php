Delete Cart

<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อน'); window.location='../Login.php';</script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_detail_id'])) {
    $cart_detail_id = (int)$_POST['cart_detail_id'];
    $user_id = $_SESSION['user_id'];

    // ลบเฉพาะสินค้าที่อยู่ในตะกร้าของ user คนนั้น
    $sql = "
        DELETE cd FROM cart_detail cd
        JOIN cart c ON c.cart_id = cd.cart_id
        WHERE cd.cart_detail_id = ? AND c.user_id = ?
    ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ii", $cart_detail_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>alert('ลบสินค้าจากตะกร้าแล้ว'); window.location='cart.php';</script>";
    } else {
        echo "<script>alert('ลบไม่สำเร็จ: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    $stmt->close();
}
?>
