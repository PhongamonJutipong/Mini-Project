<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Purchased Images</title>
    <link rel="stylesheet" href="../css/StyleUsergallery3.css">
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

    <!-- NAVBAR -->
    <nav class="navbar">
        <div class="brand">
            <a href="main.php"><h1>Pixora</h1></a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="mainbox">
        <h2>Gallery Purchased Images</h2>

        <div class="gallery">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="img-box">';
                    echo '<img src="./image_product/' . htmlspecialchars($row["product_path"]) . '" alt="' . htmlspecialchars($row["product_name"]) . '">';
                    echo '<h3>' . htmlspecialchars($row["product_name"]) . '</h3>';
                    echo '<small>Order when: ' . htmlspecialchars($row["order_date"]) . '</small>';
                    echo '</div>';
                }
            } else {
                echo "<p>🛒 You have not purchased any images yet.</p>";
            }
            ?>
        </div>
    </div>

</body>

</html>