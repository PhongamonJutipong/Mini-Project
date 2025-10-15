<?php
session_start();
require __DIR__ . '/conn.php';

/* ---------- Base paths ---------- */
$projectFsBase  = dirname(__DIR__);
$projectUrlBase = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($projectUrlBase === DIRECTORY_SEPARATOR) $projectUrlBase = '';
$projectUrlBase = rtrim($projectUrlBase, '/');

$imageDirFs    = __DIR__ . '/image_user';
$productDirFs  = __DIR__ . '/image_product';
$iconDirUrl    = $projectUrlBase . '/php/picture_and_video/';
$defaultUrl    = $projectUrlBase . '/php/assets/default-avatar.png';
$productDirUrl = $projectUrlBase . '/php/image_product/';

/* ---------- ดึงข้อมูลผู้ใช้ปัจจุบัน ---------- */
$pic = $_SESSION['user_picture'] ?? null;
if (!$pic && !empty($_SESSION['user_id'])) {
    $stmt = $mysqli->prepare("SELECT user_picture FROM user WHERE user_id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->bind_result($pic);
    $stmt->fetch();
    $stmt->close();
    if ($pic) $_SESSION['user_picture'] = $pic;
}
$picSrc = (!empty($pic) && is_file($imageDirFs . '/' . basename($pic)))
    ? $projectUrlBase . '/php/image_user/' . rawurlencode(basename($pic))
    : $defaultUrl;

/* ---------- รับค่า id จาก URL ---------- */
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
    header("Location: main.php");
    exit;
}

/* ---------- ดึงข้อมูลสินค้า ---------- */
$stmt = $mysqli->prepare("
    SELECT p.*, u.user_name AS creator_name
    FROM product p
    LEFT JOIN user u ON p.creator_id = u.user_id
    WHERE p.product_id = ?
    LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: main.php");
    exit;
}

/* ---------- ตรวจสอบรูปสินค้า ---------- */
$fileSafe = basename($product['product_path'] ?? '');
$imgFs    = $productDirFs . '/' . $fileSafe;
$imgUrl   = is_file($imgFs)
    ? $productDirUrl . rawurlencode($fileSafe)
    : $projectUrlBase . '/php/assets/no-image.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['product_name']) ?> | Pixora</title>
  <link rel="stylesheet" href="../css/StyleProduct.css">
</head>
<body>

<header class="site-header">
  <div class="topnav">
    <a href="main.php" class="brand"><h1>Pixora</h1></a>
    <div class="top-actions">
      <a href="cart.php" class="icon-btn" title="Cart">
        <img src="<?= $iconDirUrl ?>shopping-cart.png" alt="Cart">
      </a>
      <a href="profile.php" class="icon-btn" title="Profile">
        <img src="<?= htmlspecialchars($picSrc) ?>" alt="Profile" width="38" height="38">
      </a>
    </div>
  </div>
</header>

<main class="layout layout--product">
  <section class="block block--media">
    <figure class="product-figure sticky">
      <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
    </figure>
  </section>

  <section class="block block--info">
    <header class="p-header">
      <h2 class="p-title"><?= htmlspecialchars($product['product_name']) ?></h2>
      <div class="p-badges">
        <span class="pill"><?= htmlspecialchars($product['categories_name']) ?></span>
        <span class="pill"><?= htmlspecialchars(ucfirst($product['product_status'])) ?></span>
      </div>
    </header>

    <div class="p-cta sticky">
      <div class="price">฿<?= number_format((float)$product['product_price'], 2) ?></div>
      <div class="cta-actions">
        <form method="post" action="add_to_cart.php">
          <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
          <button class="btn">Add to cart</button>
        </form>
        <a class="btn btn--ghost" href="main.php">Back to list</a>
      </div>
    </div>

    <div class="p-body">
      <?php if (!empty($product['product_description'])): ?>
        <p><?= nl2br(htmlspecialchars($product['product_description'])) ?></p>
      <?php else: ?>
        <p class="muted">No description available.</p>
      <?php endif; ?>
    </div>

    <footer class="p-meta">
      <p class="muted">
        Creator: <?= htmlspecialchars($product['creator_name'] ?? 'Unknown') ?>
        • Created at: <?= htmlspecialchars($product['product_createat']) ?>
      </p>
    </footer>
  </section>
</main>

</body>
</html>
