<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>My Purchased Images</title>
<link rel="stylesheet" href="../css/StyleUsergallery1.css">
</head>

<body>
<?php
require 'conn.php'; 
session_start();

$user_id = $_SESSION['user_id'] ?? ''; 

$sql = "
    SELECT 
        p.product_id,
        p.product_name,
        p.product_path,
        p.product_price,
        o.order_id,
        o.order_date
    FROM orders AS o
    JOIN order_detail AS od ON o.order_id = od.order_id
    JOIN product AS p ON od.product_id = p.product_id
    WHERE o.user_id = ?
    ORDER BY o.order_date DESC
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<h2 style="text-align:center;">Gallery</h2>
<div class="mainbox">
    
    <div class="gallery">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<div class="img-box">';
                echo '<img src="./image_product/' . htmlspecialchars($row["product_path"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '">';
                echo '<h3>' . htmlspecialchars($row["product_name"]) . '</h3>';
                echo '<small>สั่งซื้อเมื่อ: ' . htmlspecialchars($row["order_date"]) . '</small>';
                echo '</div>';
            }
        } else {
            echo "<p style='text-align:center;'>You didn't buy any picture!</p>";
        }
        ?>
    </div>
</div>

</body>
</html>