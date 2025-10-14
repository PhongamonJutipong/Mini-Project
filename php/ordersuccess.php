<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนทำการสั่งซื้อ'); window.location='../Login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ 1. ตรวจสอบว่าสินค้ามีในตะกร้าไหม
$sql_cart = "
    SELECT cd.cart_detail_id, cd.product_id, cd.price_snap_shot, cd.sub_total, c.cart_id
    FROM cart_detail cd
    JOIN cart c ON cd.cart_id = c.cart_id
    WHERE c.user_id = ?
";

$stmt = $mysqli->prepare($sql_cart);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('ไม่มีสินค้าในตะกร้า'); window.location='cart.php';</script>";
    exit;
}

// ✅ 2. สร้างคำสั่งซื้อในตาราง orders
$order_sql = "INSERT INTO orders (user_id, order_date) VALUES (?, NOW())";
$stmt_order = $mysqli->prepare($order_sql);
$stmt_order->bind_param("i", $user_id);
$stmt_order->execute();
$order_id = $mysqli->insert_id; // เอา order_id มาใช้ต่อ

// ✅ 3. เพิ่มข้อมูลสินค้าใน order_detail
while ($item = $result->fetch_assoc()) {
    $sql_detail = "INSERT INTO order_detail (order_id, product_id, price_each) VALUES (?, ?, ?)";
    $stmt_detail = $mysqli->prepare($sql_detail);
    $stmt_detail->bind_param("iid", $order_id, $item['product_id'], $item['price_snap_shot']);
    $stmt_detail->execute();
}

// ✅ 4. ลบสินค้าทั้งหมดในตะกร้า
$del_sql = "
    DELETE cd FROM cart_detail cd
    JOIN cart c ON cd.cart_id = c.cart_id
    WHERE c.user_id = ?
";
$stmt_del = $mysqli->prepare($del_sql);
$stmt_del->bind_param("i", $user_id);
$stmt_del->execute();

// ✅ 5. แจ้งเตือนและพาไปหน้า Gallery
echo "<script>alert('สั่งซื้อสำเร็จ!'); window.location='user_gallery.php';</script>";
exit;
?>