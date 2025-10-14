<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('กรุณาเข้าสู่ระบบก่อนเพิ่มสินค้า'); window.location='login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];
    $cart_id = ("SELECT  cart_id FROM cart WHERE user_id = $user_id");

    if (empty($cart_id)) {
        $create_cart = $mysqli->prepare("INSERT INTO cart (user_id) VALUES (?)");
        $create_cart->bind_param("i", $user_id);
        $create_cart->execute();
        $cart_id = $create_cart->insert_id; // เอา cart_id ที่เพิ่งสร้าง
        $create_cart->close();

    } else {
        $cart_id = $mysqli->query($cart_id)->fetch_object()->cart_id;
    }

    // ตรวจสอบว่าสินค้าอยู่ในตะกร้าแล้วหรือยัง
    $check = $mysqli->prepare("SELECT cart_id FROM cart WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('สินค้าอยู่ในตะกร้าแล้ว');</script>";
    } else {
        $stmt = $mysqli->prepare("INSERT INTO cart (user_id, product_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $product_id);
        if ($stmt->execute()) {
            echo "<script>alert(' เพิ่มสินค้าเข้าตะกร้าเรียบร้อย');</script>";
        } else {
            echo "<script>alert(' ไม่สามารถเพิ่มสินค้าได้: " . htmlspecialchars($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
    $check->close();
}
?>