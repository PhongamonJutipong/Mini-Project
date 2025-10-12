<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>Image Gallery</title>
</head>

<link rel="stylesheet" href="../css/StyleUsergallery.css">
<body>
    <?php
        require 'conn.php'; 

    $sql = "SELECT u.user_id, p.product_id, p.product_path FROM  users_product AS u
        JOIN product AS p ON  u.product_id = p.product_id";
    $result = mysqli_query($conn, $sql);
    ?>

    <h2 style="text-align:center;">Gallery</h2>
    <div class="gallery">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<img src="' . htmlspecialchars($row["product_path"]) . '" alt="Image">';
            }
        } else {
            echo "<p>ไม่มีรูปภาพในฐานข้อมูล</p>";
        }
        ?>
    </div>
</body>
</html>