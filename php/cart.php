<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pixora · My Cart</title>
  <link rel="stylesheet" href="../css/StyleCart.css">
</head>
<body>

<header class="site-header">
  <div class="topnav">
    <a href="index.php" class="brand">
      <h1>Pixora</h1>
    </a>
    <div class="top-actions">
      <a href="gallery.php" class="btn-link">กลับหน้า Gallery</a>
      <a href="logout.php" class="btn-link logout">ออกจากระบบ</a>
    </div>
  </div>
</header>

<main class="container">
  <h2 class="page-title">your cart</h2>

  <section class="cart-container">

    <div class="cart-item">
      <div class="cart-thumb">
        <img src="../uploads/sample1.jpg" alt="สินค้า 1">
      </div>
      <div class="cart-info">
        <h3 class="cart-title">ภาพ Digital Art 01</h3>
        <p class="cart-desc">ผลงานศิลปะดิจิทัลสุดพิเศษจากศิลปิน Pixora</p>
        <p class="cart-price">250 บาท</p>
      </div>
      <div class="cart-actions">
        <button class="btn btn-danger">ลบ</button>
      </div>
    </div>

    <div class="cart-item">
      <div class="cart-thumb">
        <img src="../uploads/sample2.jpg" alt="สินค้า 2">
      </div>
      <div class="cart-info">
        <h3 class="cart-title">ภาพถ่ายธรรมชาติ</h3>
        <p class="cart-desc">ภาพถ่ายวิวธรรมชาติความละเอียดสูง</p>
        <p class="cart-price">180 บาท</p>
      </div>
      <div class="cart-actions">
        <button class="btn btn-danger">ลบ</button>
      </div>
    </div>

  </section>

  <section class="cart-summary">
    <h3>สรุปคำสั่งซื้อ</h3>
    <div class="summary-row">
      <span>ยอดรวมสินค้า:</span>
      <strong>430 บาท</strong>
    </div>
    <div class="summary-row">
      <span>ค่าธรรมเนียม:</span>
      <strong>10 บาท</strong>
    </div>
    <div class="summary-row total">
      <span>ยอดชำระทั้งหมด:</span>
      <strong>440 บาท</strong>
    </div>
    <button class="btn btn-checkout">ดำเนินการชำระเงิน</button>
  </section>
</main>

<footer class="site-footer">
  <p>© 2025 Pixora Digital Market</p>
</footer>

</body>
</html>
