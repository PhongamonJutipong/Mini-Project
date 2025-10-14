<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อนทำการสั่งซื้อ'); window.location='../Login.php';</script>";
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
<title>Pixora · สรุปคำสั่งซื้อ</title>
<link rel="stylesheet" href="css\Styleorders.css">
</head>

<body>
<header class="site-header">
  <h1>🛍️ Pixora</h1>
</header>

<main class="container">
  <h2>สรุปรายการสั่งซื้อ</h2>

  <?php if (!empty($items)): ?>
    <table class="order-table">
      <tr>
        <th>สินค้า</th>
        <th>ราคา</th>
        <th>รวม</th>
      </tr>
      <?php foreach ($items as $item): ?>
        <tr>
          <td>
            <img src="../uploads/<?= htmlspecialchars($item['product_path']) ?>" 
                 alt="<?= htmlspecialchars($item['product_name']) ?>" 
                 style="width:80px; height:auto; vertical-align:middle;"> 
            <?= htmlspecialchars($item['product_name']) ?>
          </td>
          <td><?= number_format($item['price_snap_shot'], 2) ?> ฿</td>
          <td><?= number_format($item['sub_total'], 2) ?> ฿</td>
        </tr>
      <?php endforeach; ?>
      <tr>
        <td colspan="2" style="text-align:right;"><strong>รวมทั้งหมด:</strong></td>
        <td><strong><?= number_format($total, 2) ?> ฿</strong></td>
      </tr>
    </table>

    <form action="ordersuccess.php" method="POST" style="text-align:center; margin-top:20px;">
      <button type="submit" class="btn btn-confirm">✅ ยืนยันการสั่งซื้อ</button>
    </form>

  <?php else: ?>
    <p style="text-align:center;">ยังไม่มีสินค้าในตะกร้าเลยโย่ 🛒</p>
  <?php endif; ?>
</main>
</body>
</html>