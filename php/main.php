<?php
session_start();
require __DIR__ . '/conn.php';

/* ----------------------------------------------------
   1) คำนวณฐานโปรเจกต์ (ทั้ง URL และ Filesystem)
---------------------------------------------------- */
$projectFsBase  = dirname(__DIR__);
$projectUrlBase = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($projectUrlBase === DIRECTORY_SEPARATOR) $projectUrlBase = '';

$imageDirFs   = $projectFsBase . '/image_user';
$imageDirUrl  = $projectUrlBase . '/image_user/';
$productDirUrl = $projectUrlBase . '/image_product/';
$iconDirUrl   = $projectUrlBase . '/picture_and_video/';
$defaultUrl   = $projectUrlBase . '/assets/default-avatar.png';

/* ----------------------------------------------------
   2) โหลดชื่อไฟล์รูปโปรไฟล์จาก session/DB
---------------------------------------------------- */
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

/* ----------------------------------------------------
   3) สร้าง src รูปโปรไฟล์อย่างปลอดภัย
---------------------------------------------------- */
if (!empty($pic)) {
  if (preg_match('~^(https?://|/)~', $pic)) {
    $picSrc = $pic;
  } else {
    $safe   = basename($pic);
    $fsPath = $imageDirFs . DIRECTORY_SEPARATOR . $safe;
    $picSrc = (is_file($fsPath)) ? $imageDirUrl . rawurlencode($safe) : $defaultUrl;
  }
} else {
  $picSrc = $defaultUrl;
}

/* ----------------------------------------------------
   4) ดึงรูปสินค้า จากตาราง product
---------------------------------------------------- */
$stmtPop = $mysqli->prepare("
  SELECT product_name, product_path
  FROM product
  ORDER BY product_createat DESC
  LIMIT 8
");
$stmtPop->execute();
$resultPop = $stmtPop->get_result();

$stmtRnd = $mysqli->prepare("
  SELECT product_name, product_path
  FROM product
  ORDER BY RAND()
  LIMIT 8
");
$stmtRnd->execute();
$resultRnd = $stmtRnd->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Main Page</title>
  <link rel="stylesheet" href="../css/StyleMain.css">
</head>

<body>
  <div class="page">
    <header class="site-header">
      <div class="topnav">
        <a href="index.html" class="brand" aria-label="Pixora Home">
          <h1>Pixora</h1>
        </a>

        <div class="search-container">
          <form class="search-box" action="/action_page.php" role="search">
            <input type="text" class="search-input" placeholder="Search..." name="q">
            <button type="submit" class="search-btn">Search</button>
          </form>
        </div>

        <div class="top-actions" aria-label="User actions">
          <img src="<?= htmlspecialchars($iconDirUrl) ?>shopping-cart.png" alt="Shopping cart">
          <img src="<?= htmlspecialchars($iconDirUrl) ?>favorite.png" alt="Favorites">
          <img src="<?= htmlspecialchars($picSrc, ENT_QUOTES) ?>" alt="Profile"
            width="100" height="100"
            style="border-radius:50%;object-fit:cover">
        </div>
      </div>
    </header>

    <main class="layout">
      <aside class="sidebar">
        <ul>
          <li><a href="#">Art & Design</a></li>
          <li><a href="#">Health & Fitness</a></li>
          <li><a href="#">Technology & Business</a></li>
          <li><a href="#">Travel & Adventure</a></li>
          <li><a href="#">Food & Drink</a></li>
        </ul>
      </aside>

      <section class="block">
        <h2>Popular Today</h2>
        <div class="card-grid">
          <?php if ($resultPop && $resultPop->num_rows): ?>
            <?php while ($row = $resultPop->fetch_assoc()): ?>
              <?php
              $safeFile = basename($row['product_path']);
              $imgPath  = $productDirUrl . rawurlencode($safeFile);

              // (ทางเลือก) ถ้าอยากมีรูป fallback เมื่อไฟล์หาย
              // $fallback = $projectUrlBase . '/assets/no-image.png';
              // if (!is_file($productDirFs . DIRECTORY_SEPARATOR . $safeFile)) {
              //   $imgPath = $fallback;
              // }
              ?>
              <article class="card">
                <img src="<?= htmlspecialchars($imgPath) ?>"
                  alt="<?= htmlspecialchars($row['product_name']) ?>" />
                <h3 class="card-title"><?= htmlspecialchars($row['product_name']) ?></h3>
              </article>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No products yet.</p>
          <?php endif; ?>
        </div>
      </section>

      <!-- Random Picture -->
      <section class="block">
        <h2>Random Picture</h2>
        <div class="card-grid">
          <?php if ($resultRnd && $resultRnd->num_rows): ?>
            <?php while ($row = $resultRnd->fetch_assoc()): ?>
              <?php
              $safeFile = basename($row['product_path']);
              $imgPath  = $productDirUrl . rawurlencode($safeFile);
              ?>
              <article class="card">
                <img src="<?= htmlspecialchars($imgPath) ?>"
                  alt="<?= htmlspecialchars($row['product_name']) ?>" />
                <h3 class="card-title"><?= htmlspecialchars($row['product_name']) ?></h3>
              </article>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No products yet.</p>
          <?php endif; ?>
        </div>
      </section>
      </section>
    </main>
  </div>
</body>

</html>