<?php
session_start();
require 'conn.php';

// 🔒 ต้องล็อกอินก่อนถึงจะเข้าหน้านี้ได้
if (!isset($_SESSION['user_id'])) {
  header("Location: Login.php");
  exit();
}

$user_id = $_SESSION['user_id'];

// ✅ ดึงสินค้าที่ user นี้เคยเพิ่ม
$sql = "SELECT * FROM product WHERE creator_id = ? ORDER BY product_createat DESC";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Products | Pixora</title>
  <link rel="stylesheet" href="../css/StyleUserProduct2.css">
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
    <div class="page-header">
      <h2 class="page-title">📷 สินค้าที่ฉันลงขาย</h2>
      <div class="header-actions">
        <a href="addimage.php" class="btn btn-add">➕ เพิ่มสินค้าใหม่</a>
        <a href="main.php" class="btn btn-back">🏠 กลับหน้าแรก</a>
      </div>
    </div>

    <div class="table-wrapper">
      <table class="product-table">
        <thead>
          <tr>
            <th>รูปภาพ</th>
            <th>ชื่อสินค้า</th>
            <th>หมวดหมู่</th>
            <th>ราคา</th>
            <th>สถานะ</th>
            <th>วันที่เพิ่ม</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td class="img-cell">
                  <img src="./image_product/<?= htmlspecialchars($row['product_path']) ?>" alt="<?= htmlspecialchars($row['product_name']) ?>">
                </td>
                <td class="name-cell"><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= htmlspecialchars($row['categories_name']) ?></td>
                <td class="price-cell">฿<?= number_format($row['product_price'], 2) ?></td>
                <td>
                  <span class="status <?= strtolower($row['product_status'] ?? 'pending') ?>">
                    <?= $row['product_status'] ? htmlspecialchars($row['product_status']) : 'รอตรวจสอบ' ?>
                  </span>
                </td>
                <td class="date-cell"><?= htmlspecialchars($row['product_createat']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="empty">
                <div class="empty-state">
                  <p>🖼️ คุณยังไม่ได้เพิ่มสินค้าเลย</p>
                  <a href="addimage.php" class="btn btn-add">เริ่มเพิ่มสินค้าตอนนี้</a>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

  <!-- FOOTER -->
  <footer class="footer">
    <p>© 2025 Pixora. All rights reserved.</p>
  </footer>

</body>

</html>