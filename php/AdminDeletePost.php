<?php
require 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = $_POST['product_id'];

    $sql1 = "DELETE FROM user_product WHERE up_idproduct = '$id'";
    mysqli_query($mysqli, $sql1);

    $sql2 = "DELETE FROM product WHERE product_id = '$id'";
    if (mysqli_query($mysqli, $sql2)) {
        echo "<script>alert('The post has been successfully deleted.');</script>";
    } else {
        echo "<script>alert('Failed to delete the post: " . mysqli_error($mysqli) . "');</script>";
    }
}
?>
