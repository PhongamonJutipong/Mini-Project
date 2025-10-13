<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Image Gallery</title>
<link rel="stylesheet" href="../css/StyleUsergallery.css">
</head>

<body>

<?php
require 'conn.php'; 

// ✅ ใช้ตาราง user_product (สัมพันธ์ระหว่าง user และ product)
$sql = "SELECT u.user_id, p.product_id, p.product_path
        FROM user_product AS up
        JOIN user AS u ON up.up_iduser = u.user_id
        JOIN product AS p ON up.up_idproduct = p.product_id";

$result = mysqli_query($mysqli, $sql);
?>

<h2 style="text-align:center;">Gallery</h2>
<div class="gallery">
    <?php
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<img src="../image_product/' . htmlspecialchars($row["product_path"]) . '" alt="Image">';
        }
    } else {
        echo "<p>ไม่มีรูปภาพในฐานข้อมูล</p>";
    }
    ?>
</div>

</body>
</html>