<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login before adding products to cart.'); window.location='../Login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = (int)$_POST['product_id'];

/* ตรวจสอบว่าผู้ใช้มี cart แล้วหรือยัง */
$stmt = $mysqli->prepare("SELECT cart_id FROM cart WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($cart_id);
$stmt->fetch();
$stmt->close();

if (empty($cart_id)) {
    $stmt = $mysqli->prepare("INSERT INTO cart (user_id) VALUES (?)");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $cart_id = $stmt->insert_id;
    $stmt->close();
}

/* ดึงราคาสินค้าปัจจุบันจาก product */
$stmt = $mysqli->prepare("SELECT product_price FROM product WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($product_price);
$stmt->fetch();
$stmt->close();

if (!$product_price) {
    echo "<script>alert('Product not found.'); history.back();</script>";
    exit;
}

/* ตรวจสอบว่าสินค้านี้อยู่ในตะกร้าแล้วหรือยัง */
$stmt = $mysqli->prepare("SELECT cart_detail_id FROM cart_detail WHERE cart_id = ? AND product_id = ?");
$stmt->bind_param("ii", $cart_id, $product_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('This product is already in your cart.'); history.back();</script>";
    $stmt->close();
} else {
    $stmt->close();

    /* เพิ่มสินค้าใหม่ใน cart_detail */
    $sub_total = $product_price; 
    $stmt = $mysqli->prepare("
        INSERT INTO cart_detail (cart_id, product_id, price_snap_shot, sub_total)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->bind_param("iidd", $cart_id, $product_id, $product_price, $sub_total);
    if ($stmt->execute()) {
        echo "<script>alert('Product added to cart successfully.'); history.back();</script>";
    } else {
        echo "<script>alert('Failed to add product: " . htmlspecialchars($stmt->error) . "');</script>";
    }
    $stmt->close();
}
?>