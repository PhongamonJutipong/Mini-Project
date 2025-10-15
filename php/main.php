<?php
session_start();
require __DIR__ . '/conn.php';

/* ------------ 1) Base paths (normalize + safe) ------------ */
/*
  โครงสร้าง (อ้างจากโปรเจกต์ของคุณ)
  - ไฟล์นี้อยู่:          /Mini Project/php/THIS_FILE.php
  - โฟลเดอร์รูปผู้ใช้:    /Mini Project/php/image_user
  - โฟลเดอร์รูปสินค้า:    /Mini Project/php/image_product
  - โฟลเดอร์รูปไอคอน:     /Mini Project/php/picture_and_video
  - โฟลเดอร์ assets:      /Mini Project/php/assets
*/
$projectFsBase  = dirname(__DIR__);                          // C:\xampp\htdocs\Mini Project
$projectUrlBase = dirname(dirname($_SERVER['SCRIPT_NAME'])); // /Mini Project หรือ ''
if ($projectUrlBase === DIRECTORY_SEPARATOR) $projectUrlBase = '';
$projectUrlBase       = rtrim($projectUrlBase, '/');
$projectUrlBaseSafe   = str_replace(' ', '%20', $projectUrlBase);

$imageDirFs    = __DIR__ . '/image_user';
$productDirFs  = __DIR__ . '/image_product';
$iconDirFs     = __DIR__ . './picture and video';
$assetsDirFs   = __DIR__ . '/assets';

$imageDirUrl   = $projectUrlBaseSafe . '/php/image_user/';
$productDirUrl = $projectUrlBaseSafe . '/php/image_product/';
$iconDirUrl    = $projectUrlBaseSafe . '/php/picture_and_video/';
$assetsDirUrl  = $projectUrlBaseSafe . '/php/assets/';

/* ------------ 2) Helpers ------------ */
function escape_like($s)
{
  return str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $s);
}

function activeAttr(string $currentCat, string $label): string
{
  $isActive = ($label === 'ALL') ? ($currentCat === '') : ($currentCat === $label);
  return $isActive ? ' aria-current="page" class="active"' : '';
}

function buildUrl(array $params = []): string
{
  $self = $_SERVER['PHP_SELF'];
  return htmlspecialchars($self . (empty($params) ? '' : ('?' . http_build_query($params))));
}

/* สร้าง src แบบตรวจไฟล์ + กันแคช */
function build_asset_src(string $fsDir, string $urlDir, string $filename, ?string $fallback = null): string
{
  $fs = rtrim($fsDir, '/\\') . '/' . $filename;
  if (is_file($fs)) {
    return $urlDir . rawurlencode($filename) . '?v=' . filemtime($fs);
  }
  return $fallback ?? ($urlDir . rawurlencode($filename));
}

/* ------------ 3) Default avatar ------------ */
$defaultAvatar = is_file($assetsDirFs . '/default-avatar.png')
  ? $assetsDirUrl . 'default-avatar.png?v=' . filemtime($assetsDirFs . '/default-avatar.png')
  : build_asset_src($iconDirFs, $iconDirUrl, 'IMG_logo.jpg', $projectUrlBaseSafe . '/php/assets/default-avatar.png');

/* ------------ 4) Profile picture (รองรับ URL เต็ม/ไฟล์ในโฟลเดอร์) ------------ */
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

$picSrc = $defaultAvatar;
if (!empty($pic)) {
  if (preg_match('~^https?://~i', $pic)) {
    $picSrc = htmlspecialchars($pic);
  } else {
    $bn = basename($pic);
    $picSrc = build_asset_src($imageDirFs, $imageDirUrl, $bn, $defaultAvatar);
  }
}

/* ------------ 5) เตรียม src ของไอคอน topnav ------------ */
$cartSrc = build_asset_src($iconDirFs, $iconDirUrl, 'shoponic-cart.png');         // ต้องมีไฟล์นี้จริง
$favSrc  = build_asset_src($iconDirFs, $iconDirUrl, 'favorite.png');              // ต้องมีไฟล์นี้จริง
/* ถ้าต้องการ fallback เพิ่ม (เผื่อไฟล์ชื่ออื่น)
$cartSrc = build_asset_src($iconDirFs, $iconDirUrl, 'shoponic-cart.png', $iconDirUrl.'cart.png');
$favSrc  = build_asset_src($iconDirFs, $iconDirUrl, 'favorite.png', $iconDirUrl.'heart.png');
*/

/* ------------ 6) รับพารามิเตอร์ cat + q ------------ */
$q_raw  = trim($_GET['q'] ?? '');
$q      = mb_substr($q_raw, 0, 80);
$q_like = $q !== '' ? '%' . escape_like($q) . '%' : '';

$cat_raw = trim($_GET['cat'] ?? '');
$allowedCats = [
  'Art & Design',
  'Health & Fitness',
  'Technology & Business',
  'Travel & Adventure',
  'Food & Drink'
];
$cat = in_array($cat_raw, $allowedCats, true) ? $cat_raw : '';

/* ------------ 7) Query เงื่อนไข ------------ */
$filters = [];
$params  = [];
$types   = '';

if ($cat !== '') {
  $filters[] = "categories_name = ?";
  $params[]  = $cat;
  $types    .= 's';
}
if ($q !== '') {
  $filters[] = "product_name LIKE ?";
  $params[]  = $q_like;
  $types    .= 's';
}

$resultFiltered = null;
$resultPop = null;
$resultRnd = null;

if ($filters) {
  $sql = "
    SELECT product_id, product_name, product_path
    FROM product
    WHERE " . implode(' AND ', $filters) . "
    ORDER BY product_createat DESC
    LIMIT 24
  ";
  $stmt = $mysqli->prepare($sql);
  if ($types !== '') $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $resultFiltered = $stmt->get_result();
  $stmt->close();
} else {
  $stmtPop = $mysqli->prepare("
    SELECT product_id, product_name, product_path
    FROM product
    ORDER BY product_createat DESC
    LIMIT 8
  ");
  $stmtPop->execute();
  $resultPop = $stmtPop->get_result();
  $stmtPop->close();

  $stmtRnd = $mysqli->prepare("
    SELECT product_id, product_name, product_path
    FROM product
    ORDER BY RAND()
    LIMIT 8
  ");
  $stmtRnd->execute();
  $resultRnd = $stmtRnd->get_result();
  $stmtRnd->close();
}

/* query string ติดการ์ดไว้พากลับ (พก cat/q) */
$carry = array_filter([
  'cat' => $cat ?: null,
  'q'   => $q   ?: null,
]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pixora | Main</title>
  <link rel="stylesheet" href="../css/StyleMain4.css">
</head>

<body>
  <header class="site-header">
    <div class="topnav">
      <a href="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" class="brand" aria-label="Pixora Home">
        <h1>Pixora</h1>
      </a>

      <div class="search-container">
        <form class="search-box" method="get" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>" role="search" aria-label="Site search">
          <?php if ($cat !== ''): ?>
            <input type="hidden" name="cat" value="<?= htmlspecialchars($cat) ?>">
          <?php endif; ?>
          <input type="text" class="search-input" name="q" placeholder="Search images..." value="<?= htmlspecialchars($q_raw) ?>">
          <button type="submit" class="search-btn">Search</button>
        </form>
      </div>

      <div class="top-actions" aria-label="User actions">
        <a href="cart.php" class="icon-btn" title="Cart">
          <img src="./picture and video/shopping-cart.png" alt="">
        </a>

        <a href="addimage.php" class="icon-btn" title="Favorite">
          <img src="./picture and video/addimage.png" alt="add image">
        </a>

        <a href="user_gallery.php" class="icon-btn" title="gallery">
          <img src="./picture and video/photo.png" alt="gallery">
        </a>

        <a href="product.php" class="icon-btn" title="product">
          <img src="./picture and video/product.png" alt="product">
        </a>

        <a href="profile.php" class="icon-btn" title="Profile">
          <img
            src="<?= htmlspecialchars($picSrc) ?>"
            alt="Profile" width="42" height="42"
            style="border-radius:50%; object-fit:cover; border:2px solid rgba(0,0,0,.06); box-shadow:0 2px 8px rgba(0,0,0,.12);"
            onerror="this.onerror=null; this.src='<?= $defaultAvatar ?>';">
        </a>
      </div>
    </div>
  </header>

  <main class="layout">
    <aside class="sidebar">
      <ul>
        <?php $base = [];
        if ($q !== '') $base['q'] = $q; ?>
        <li><a href="<?= buildUrl($base) ?>" <?= activeAttr($cat, 'ALL') ?>>All</a></li>
        <li><a href="<?= buildUrl($base + ['cat' => 'Art & Design']) ?>" <?= activeAttr($cat, 'Art & Design') ?>>Art & Design</a></li>
        <li><a href="<?= buildUrl($base + ['cat' => 'Health & Fitness']) ?>" <?= activeAttr($cat, 'Health & Fitness') ?>>Health & Fitness</a></li>
        <li><a href="<?= buildUrl($base + ['cat' => 'Technology & Business']) ?>" <?= activeAttr($cat, 'Technology & Business') ?>">Technology & Business</a></li>
        <li><a href="<?= buildUrl($base + ['cat' => 'Travel & Adventure']) ?>" <?= activeAttr($cat, 'Travel & Adventure') ?>">Travel & Adventure</a></li>
        <li><a href="<?= buildUrl($base + ['cat' => 'Food & Drink']) ?>" <?= activeAttr($cat, 'Food & Drink') ?>">Food & Drink</a></li>
      </ul>
    </aside>

    <div class="content">
      <?php if (!empty($filters)): ?>
        <section class="block">
          <h2>
            Results for
            <?= $cat ? htmlspecialchars($cat) . ' ' : '' ?>
            <?= $q ? '“' . htmlspecialchars($q_raw) . '”' : '' ?>
          </h2>
          <div class="card-grid">
            <?php if (!empty($resultFiltered) && $resultFiltered->num_rows): ?>
              <?php while ($r = $resultFiltered->fetch_assoc()): ?>
                <?php
                $file = basename($r['product_path']);
                $img  = $productDirUrl . rawurlencode($file);
                $qs   = http_build_query($carry + ['id' => (int)$r['product_id']]);
                ?>
                <a class="card" href="product.php?<?= $qs ?>">
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($r['product_name']) ?>">
                  <h3 class="card-title"><?= htmlspecialchars($r['product_name']) ?></h3>
                </a>
              <?php endwhile; ?>
            <?php else: ?>
              <p>No results found.</p>
            <?php endif; ?>
          </div>
        </section>
      <?php else: ?>
        <section class="block">
          <h2>Popular Today</h2>
          <div class="card-grid">
            <?php if ($resultPop && $resultPop->num_rows): ?>
              <?php while ($r = $resultPop->fetch_assoc()): ?>
                <?php
                $file = basename($r['product_path']);
                $img  = $productDirUrl . rawurlencode($file);
                $qs   = http_build_query($carry + ['id' => (int)$r['product_id']]);
                ?>
                <a class="card" href="product.php?<?= $qs ?>">
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($r['product_name']) ?>">
                  <h3 class="card-title"><?= htmlspecialchars($r['product_name']) ?></h3>
                </a>
              <?php endwhile; ?>
            <?php else: ?>
              <p>No popular items yet.</p>
            <?php endif; ?>
          </div>
        </section>

        <section class="block">
          <h2>Random Picture</h2>
          <div class="card-grid">
            <?php if ($resultRnd && $resultRnd->num_rows): ?>
              <?php while ($r = $resultRnd->fetch_assoc()): ?>
                <?php
                $file = basename($r['product_path']);
                $img  = $productDirUrl . rawurlencode($file);
                $qs   = http_build_query($carry + ['id' => (int)$r['product_id']]);
                ?>
                <a class="card" href="product.php?<?= $qs ?>">
                  <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($r['product_name']) ?>">
                  <h3 class="card-title"><?= htmlspecialchars($r['product_name']) ?></h3>
                </a>
              <?php endwhile; ?>
            <?php else: ?>
              <p>No random items yet.</p>
            <?php endif; ?>
          </div>
        </section>
      <?php endif; ?>
    </div>
  </main>
</body>

</html>