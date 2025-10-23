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
  <link rel="stylesheet" href="../css/StyleUserProduct3.css">
</head>

<body>

  <!-- NAVBAR -->
  <nav class="navbar">
    <div class="brand">
      <a href="main.php"><h1>Pixora</h1></a>
    </div>
  </nav>

  <!-- MAIN CONTENT -->
  <main class="container">
    <div class="page-header">
      <h2 class="page-title">📷 Products for Sale</h2>
      <div class="header-actions">
        <a href="addimage.php" class="btn btn-add">➕ Add new products</a>
        <a href="main.php" class="btn btn-back">🏠 Return to home page</a>
      </div>
    </div>

    <div class="table-wrapper">
      <table class="product-table">
        <thead>
          <tr>
            <th>picture</th>
            <th>Product name</th>
            <th>Category</th>
            <th>price</th>
            <th>status</th>
            <th>Date added</th>
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
                    <?= $row['product_status'] ? htmlspecialchars($row['product_status']) : 'Waiting to check' ?>
                  </span>
                </td>
                <td class="date-cell"><?= htmlspecialchars($row['product_createat']) ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6" class="empty">
                <div class="empty-state">
                  <p>🖼️ You haven't added any products yet.</p>
                  <a href="addimage.php" class="btn btn-add">Start adding products now.</a>
                </div>
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </main>

</body>

</html>