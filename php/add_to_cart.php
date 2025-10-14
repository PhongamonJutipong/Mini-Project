<?php
session_start();
require __DIR__ . '/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('กรุณาเข้าสู่ระบบก่อนเพิ่มสินค้า'); window.location='login.php';</script>";
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];

    /* 1️⃣ ตรวจสอบว่ามี cart ของ user นี้หรือยัง */
    $cart_check = $mysqli->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
    $cart_check->bind_param("i", $user_id);
    $cart_check->execute();
    $cart_check->bind_result($cart_id);
    $cart_check->fetch();
    $cart_check->close();

    /* 2️⃣ ถ้ายังไม่มี → สร้างใหม่ */
    if (empty($cart_id)) {
        $create_cart = $mysqli->prepare("INSERT INTO cart (user_id) VALUES (?)");
        $create_cart->bind_param("i", $user_id);
        $create_cart->execute();
        $cart_id = $create_cart->insert_id; // เอา cart_id ที่เพิ่งสร้าง
        $create_cart->close();
    }

    /* 3️⃣ ตรวจสอบว่าสินค้านี้มีอยู่ใน cart_item แล้วหรือยัง */
    $check_item = $mysqli->prepare("SELECT cart_detail_id FROM cart_detail WHERE cart_id = ? AND product_id = ?");
    $check_item->bind_param("ii", $cart_id, $product_id);
    $check_item->execute();
    $check_item->store_result();

    if ($check_item->num_rows > 0) {
        echo "<script>alert('สินค้าอยู่ในตะกร้าแล้ว');</script>";
    } else {
        /* 4️⃣ เพิ่มสินค้าใหม่เข้า cart_item */
        $add_item = $mysqli->prepare("INSERT INTO cart_detail (cart_id, product_id) VALUES (?, ?)");
        $add_item->bind_param("ii", $cart_id, $product_id);
        if ($add_item->execute()) {
            echo "<script>alert('✅ เพิ่มสินค้าเข้าตะกร้าเรียบร้อย');</script>";
        } else {
            echo "<script>alert('❌ ไม่สามารถเพิ่มสินค้าได้: " . htmlspecialchars($add_item->error) . "');</script>";
        }
        $add_item->close();
    }

    $check_item->close();
}
?>  