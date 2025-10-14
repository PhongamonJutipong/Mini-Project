<?php
session_start();
require __DIR__ . '/conn.php';

/* ------------ 1) Base paths ------------ */
$projectFsBase  = dirname(__DIR__);
$projectUrlBase = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($projectUrlBase === DIRECTORY_SEPARATOR) $projectUrlBase = '';
$imageDirFs    = $projectFsBase . '/php/image_user';
$imageDirUrl   = $projectUrlBase . '/php/image_user/';
$productDirUrl = $projectUrlBase . '/php/image_product/';
$iconDirUrl    = $projectUrlBase . '/php/picture_and_video/';
$defaultUrl    = $projectUrlBase . '/php/assets/default-avatar.png';

/* ------------ 2) Profile picture ------------ */
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
  ? $imageDirUrl . rawurlencode(basename($pic))
  : $defaultUrl;

/* ------------ 3) รับพารามิเตอร์ cat + q ------------ */
function escape_like($s)
{
  return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $s);
}
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

/* helper ทำไฮไลต์ + สร้าง URL */
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

/* ------------ 4) Query เงื่อนไข ------------ */
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
  <link rel="stylesheet" href="../css/StyleMain2.css">
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
        <a href=""><img src="<?= htmlspecialchars($iconDirUrl) ?>shopping-cart.png" alt="cart"></a>
        <img src="<?= htmlspecialchars($iconDirUrl) ?>favorite.png" alt="fav">
        <img src="<?= htmlspecialchars($picSrc) ?>" alt="Profile" width="38" height="38">
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
        <li><a href="<?= buildUrl($base + ['cat' => 'Technology & Business']) ?>" <?= activeAttr($cat, 'Technology & Business') ?>>Technology & Business</a></li>
        <li><a href="<?= buildUrl($base + ['cat' => 'Travel & Adventure']) ?>" <?= activeAttr($cat, 'Travel & Adventure') ?>>Travel & Adventure</a></li>
        <li><a href="<?= buildUrl($base + ['cat' => 'Food & Drink']) ?>" <?= activeAttr($cat, 'Food & Drink') ?>>Food & Drink</a></li>
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
            <?php if ($resultFiltered && $resultFiltered->num_rows): ?>
              <?php while ($r = $resultFiltered->fetch_assoc()): ?>
                <?php
                $img = $productDirUrl . rawurlencode(basename($r['product_path']));
                $qs  = http_build_query($carry + ['id' => (int)$r['product_id']]);
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
            <?php while ($r = $resultPop->fetch_assoc()): ?>
              <?php
              $img = $productDirUrl . rawurlencode(basename($r['product_path']));
              $qs  = http_build_query($carry + ['id' => (int)$r['product_id']]);
              ?>
              <a class="card" href="product.php?<?= $qs ?>">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($r['product_name']) ?>">
                <h3 class="card-title"><?= htmlspecialchars($r['product_name']) ?></h3>
              </a>
            <?php endwhile; ?>
          </div>
        </section>

        <section class="block">
          <h2>Random Picture</h2>
          <div class="card-grid">
            <?php while ($r = $resultRnd->fetch_assoc()): ?>
              <?php
              $img = $productDirUrl . rawurlencode(basename($r['product_path']));
              $qs  = http_build_query($carry + ['id' => (int)$r['product_id']]);
              ?>
              <a class="card" href="product.php?<?= $qs ?>">
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($r['product_name']) ?>">
                <h3 class="card-title"><?= htmlspecialchars($r['product_name']) ?></h3>
              </a>
            <?php endwhile; ?>
          </div>
        </section>
      <?php endif; ?>
    </div>
  </main>
</body>

</html>