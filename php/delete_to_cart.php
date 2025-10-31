<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>
        alert('Please log in before continuing.');
        window.location='../Login.php';
    </script>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_detail_id'])) {
    $cart_detail_id = (int)$_POST['cart_detail_id'];
    $user_id = $_SESSION['user_id'];

    // Delete only items that belong to this user's cart
    $sql = "
        DELETE cd FROM cart_detail cd
        JOIN cart c ON c.cart_id = cd.cart_id
        WHERE cd.cart_detail_id = ? AND c.user_id = ?
    ";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param('ii', $cart_detail_id, $user_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('The item has been removed from your cart.');
            window.location='cart.php';
        </script>";
    } else {
        echo "<script>
            alert('Failed to remove the item: " . htmlspecialchars($stmt->error) . "');
        </script>";
    }

    $stmt->close();
}
?>
