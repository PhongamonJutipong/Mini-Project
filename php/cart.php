<?php
session_start();
require 'conn.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('กรุณาเข้าสู่ระบบก่อนเข้าหน้านี้'); window.location='../Login.php';</script>";
  exit;
}

$user_id = $_SESSION['user_id'];

/* ✅ ดึงสินค้าทั้งหมดในตะกร้าของ user */
$sql = "
    SELECT 
        cd.cart_detail_id,
        p.product_id,
        p.product_name,
        p.product_description,
        p.product_path,
        cd.price_snap_shot,
        cd.sub_total
    FROM cart c
    JOIN cart_detail cd ON cd.cart_id = c.cart_id
    JOIN product p ON cd.product_id = p.product_id
    WHERE c.user_id = ?
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <title>Pixora · My Cart</title>
  <link rel="stylesheet" href="../css/StyleCart.css">
</head>

<body>
<header class="site-header">
  <div class="topnav">
    <h1>🛍️ Pixora</h1>
    <div class="top-actions">
      <a href="gallery.php" class="btn-link">🏠 Gallery</a>
      <a href="logout.php" class="btn-link logout">🚪 Logout</a>
    </div>
  </div>
</header>

<main class="container">
  <h2 class="page-title">ตะกร้าสินค้าของฉัน</h2>

  <section class="cart-container">
    <?php 
    $total = 0;
    if (!empty($cart_items)): 
      foreach ($cart_items as $item):
        $total += $item['sub_total'];
    ?>
      <div class="cart-item">
        <div class="cart-thumb">
          <img src="../uploads/<?= htmlspecialchars($item['product_path']) ?>" alt="ภาพสินค้า">
        </div>
        <div class="cart-info">
          <h3 class="cart-title"><?= htmlspecialchars($item['product_name']) ?></h3>
          <p class="cart-desc"><?= htmlspecialchars($item['product_description']) ?></p>
          <p class="cart-price"><?= number_format($item['price_snap_shot'], 2) ?> บาท</p>

        </div>
        <div class="cart-actions">
          <form method="POST" action="delete_to_cart.php">
            <input type="hidden" name="cart_detail_id" value="<?= $item['cart_detail_id'] ?>">
            <button class="btn btn-danger">🗑 ลบ</button>
          </form>
        </div>
      </div>
    </div>
    <form method="POST" action="order.php">
  <button class="btn btn-checkout">✅ ชำระเงิน</button>
</form>
  </section>
</main>
</body>
</html>