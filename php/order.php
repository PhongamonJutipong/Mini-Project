<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
  echo "<script>alert('Please log in before ordering.'); window.location='../Login.php';</script>";
  exit;
}

$user_id = $_SESSION['user_id'];

$sql = "
    SELECT 
        p.product_name,
        p.product_path,
        cd.price_snap_shot,
        cd.sub_total
    FROM cart c
    JOIN cart_detail cd ON c.cart_id = cd.cart_id
    JOIN product p ON cd.product_id = p.product_id
    WHERE c.user_id = ?
";

$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$total = 0;
$items = [];

while ($row = $result->fetch_assoc()) {
  $items[] = $row;
  $total += $row['sub_total'];
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pixora Order summary</title>
  <link rel="stylesheet" href="../css/StyleOrdersuccess.css">
</head>

<body>
  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="brand">
      <h1>Pixora</h1>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <main class="container">
    <h2 class="page-title">Order summary</h2>

    <?php if (!empty($items)): ?>
      <div class="order-summary">
        <?php foreach ($items as $item): ?>
          <div class="order-item">
            <div class="item-image">
              <img src="./image_product/<?= htmlspecialchars($item['product_path']) ?>"
                alt="<?= htmlspecialchars($item['product_name']) ?>">
            </div>
            <div class="item-details">
              <h3><?= htmlspecialchars($item['product_name']) ?></h3>
              <div class="item-price">
                <span class="label">price:</span>
                <span class="price"><?= number_format($item['price_snap_shot'], 2) ?> ฿</span>
              </div>
            </div>
            <div class="item-total">
              <span class="total-amount"><?= number_format($item['sub_total'], 2) ?> ฿</span>
            </div>
          </div>
        <?php endforeach; ?>

        <div class="order-total">
          <span class="total-label">Grand total:</span>
          <span class="total-value"><?= number_format($total, 2) ?> ฿</span>
        </div>
      </div>

      <form action="ordersuccess.php" method="POST" class="confirm-form">
        <button type="submit" class="btn btn-confirm">Order confirmation</button>
        <a href="cart.php" class="btn btn-back">Return to cart</a>
      </form>

    <?php else: ?>
      <div class="empty-order">
        <p>🛒 There are no products in the cart yet.</p>
        <a href="gallery.php" class="btn btn-shop">Go choose the product.</a>
      </div>
    <?php endif; ?>
  </main>
</body>

</html>