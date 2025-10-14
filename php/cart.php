<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนเข้าหน้านี้'); window.location='../Login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// ดึงสินค้าทั้งหมดในตะกร้าของ user นี้
$sql = "SELECT p.product_id, p.product_name, p.product_price, p.product_description, p.product_path ,cd.cart_detail_id
        FROM cart c
        JOIN product p ON c.product_id = p.product_id 
        JOIN cart_detail cd ON cd.cart_id = c.cart_id 
        WHERE c.user_id = '$user_id'";
$result = mysqli_query($mysqli, $sql);
$cart_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
        $total += $item['product_price'];
    ?>
      <div class="cart-item">
        <div class="cart-thumb">
          <img src="../uploads/<?= htmlspecialchars($item['product_path']) ?>" alt="ภาพสินค้า">
        </div>
        <div class="cart-info">
          <h3 class="cart-title"><?= htmlspecialchars($item['product_name']) ?></h3>
          <p class="cart-desc"><?= htmlspecialchars($item['product_description']) ?></p>
          <p class="cart-price">💰 <?= number_format($item['product_price'], 2) ?> บาท</p>
        </div>
        <div class="cart-actions">
          <form method="POST" action="remove_from_cart.php">
            <input type="hidden" name="product_id" value="<?= $item['product_id'] ?>">
            <button class="btn btn-danger">🗑 ลบ</button>
          </form>
        </div>
      </div>
    <?php endforeach; else: ?>
      <p>🛒 ยังไม่มีสินค้าในตะกร้า</p>
    <?php endif; ?>
  </section>

  <!-- สรุปราคา -->
  <section class="cart-summary">
    <h3>สรุปคำสั่งซื้อ</h3>
    <div class="summary-row">
      <span>ยอดรวมสินค้า:</span>
      <strong><?= number_format($total, 2) ?> บาท</strong>
    </div>
    <button class="btn btn-checkout">✅ ชำระเงิน</button>
  </section>
</main>
</body>
</html>
