<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Please sign in before placing an order.');
        window.location='../Login.php';
    </script>";
    exit;
}

$user_id = $_SESSION['user_id'];

$sql_cart = "
    SELECT cd.cart_detail_id, cd.product_id, cd.price_snap_shot, cd.sub_total, c.cart_id
    FROM cart_detail cd
    JOIN cart c ON cd.cart_id = c.cart_id
    WHERE c.user_id = ?
";

$stmt = $mysqli->prepare($sql_cart);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>
        alert('Your cart is currently empty.');
        window.location='cart.php';
    </script>";
    exit;
}

// 2) Create order
$order_sql = "INSERT INTO orders (user_id, order_date) VALUES (?, NOW())";
$stmt_order = $mysqli->prepare($order_sql);
$stmt_order->bind_param('i', $user_id);
$stmt_order->execute();
$order_id = $mysqli->insert_id;

// 3) Insert order details
while ($item = $result->fetch_assoc()) {
    $sql_detail = "INSERT INTO order_detail (order_id, product_id, price, sub_total) VALUES (?, ?, ?, ?)";
    $stmt_detail = $mysqli->prepare($sql_detail);
    $stmt_detail->bind_param('iidd', $order_id, $item['product_id'], $item['price_snap_shot'], $item['sub_total']);
    $stmt_detail->execute();
}

// 4) Clear cart
$del_sql = "
    DELETE cd FROM cart_detail cd
    JOIN cart c ON cd.cart_id = c.cart_id
    WHERE c.user_id = ?
";
$stmt_del = $mysqli->prepare($del_sql);
$stmt_del->bind_param('i', $user_id);
$stmt_del->execute();

// 5) Notify and redirect
echo "<script>
    alert('Your order has been placed successfully.');
    window.location='user_gallery.php';
</script>";
exit;
?>
