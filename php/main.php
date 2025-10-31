<?php
session_start();
require __DIR__ . '/conn.php';

// ==================== ตั้งค่า Path และ URL ====================
$projectUrlBase = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($projectUrlBase === DIRECTORY_SEPARATOR) {
  $projectUrlBase = '';
}
$projectUrlBase = rtrim($projectUrlBase, '/');
$projectUrlBase = str_replace(' ', '%20', $projectUrlBase);
$imageDirFs = __DIR__ . '/image_user';
$productDirFs = __DIR__ . '/image_product';
$iconDirFs = __DIR__ . '/picture_and_video';
$assetsDirFs = __DIR__ . '/assets';
$imageDirUrl = $projectUrlBase . '/php/image_user/';
$productDirUrl = $projectUrlBase . '/php/image_product/';
$iconDirUrl = $projectUrlBase . '/php/picture_and_video/';
$assetsDirUrl = $projectUrlBase . '/php/assets/';

// ==================== ฟังก์ชันช่วย ====================
function buildAssetSrc($fsDir, $urlDir, $filename, $fallback = null)
{
  $fs = rtrim($fsDir, '/\\') . '/' . $filename;
  if (is_file($fs)) {
    return $urlDir . rawurlencode($filename) . '?v=' . filemtime($fs);
  }
  if ($fallback) {
    return $fallback;
  }
  return $urlDir . rawurlencode($filename);
}

function activeAttr($currentCat, $label)
{
  if ($label === 'ALL') {
    $isActive = empty($currentCat);
  } else {
    $isActive = ($currentCat === $label);
  }
  if ($isActive) {
    return ' aria-current="page" class="active"';
  }
  return '';
}

function buildUrl($params = array())
{
  $self = $_SERVER['PHP_SELF'];
  if (empty($params)) {
    return htmlspecialchars($self);
  }
  return htmlspecialchars($self . '?' . http_build_query($params));
}

// ==================== รูปโปรไฟล์ ====================
$defaultAvatar = buildAssetSrc(
  $assetsDirFs,
  $assetsDirUrl,
  'default-avatar.png',
  buildAssetSrc($iconDirFs, $iconDirUrl, 'IMG_logo.jpg', $assetsDirUrl . 'default-avatar.png')
);

$pic = null;
if (isset($_SESSION['user_picture'])) {
  $pic = $_SESSION['user_picture'];
}

if (!$pic && !empty($_SESSION['user_id'])) {
  $stmt = $mysqli->prepare("SELECT user_picture FROM user WHERE user_id = ?");
  $stmt->bind_param("i", $_SESSION['user_id']);
  $stmt->execute();
  $stmt->bind_result($pic);
  $stmt->fetch();
  $stmt->close();
  if ($pic) {
    $_SESSION['user_picture'] = $pic;
  }
}

$picSrc = $defaultAvatar;
if (!empty($pic)) {
  if (preg_match('~^https?://~i', $pic)) {
    $picSrc = htmlspecialchars($pic);
  } else {
    $bn = basename($pic);
    $picSrc = buildAssetSrc($imageDirFs, $imageDirUrl, $bn, $defaultAvatar);
  }
}

// ==================== รับค่าจาก URL ====================
$q = '';
if (isset($_GET['q'])) {
  $q = trim($_GET['q']);
  $q = mb_substr($q, 0, 80);
}

$allowedCats = array(
  'Art & Design',
  'Health & Fitness',
  'Technology & Business',
  'Travel & Adventure',
  'Food & Drink'
);

$cat = '';
if (isset($_GET['cat'])) {
  $catInput = trim($_GET['cat']);
  if (in_array($catInput, $allowedCats, true)) {
    $cat = $catInput;
  }
}

// ==================== สร้าง Query สำหรับค้นหา ====================
$filters = array();
$params = array();
$types = '';

if ($cat !== '') {
  $filters[] = "categories_name = ?";
  $params[] = $cat;
  $types .= 's';
}

if ($q !== '') {
  $qLike = str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $q);
  $qLike = '%' . $qLike . '%';
  $filters[] = "product_name LIKE ?";
  $params[] = $qLike;
  $types .= 's';
}

$resultFiltered = null;
$resultPop = null;
$resultRnd = null;

// ถ้ามีการค้นหาหรือเลือก category
if (count($filters) > 0) {
  $sql = "SELECT product_id, product_name, product_path FROM product WHERE "
    . implode(' AND ', $filters) . " ORDER BY product_createat DESC LIMIT 24";
  $stmt = $mysqli->prepare($sql);
  if ($types !== '') {
    $stmt->bind_param($types, ...$params);
  }
  $stmt->execute();
  $resultFiltered = $stmt->get_result();
  $stmt->close();
} else {
  // แสดงรายการ Popular
  $stmtPop = $mysqli->prepare("SELECT product_id, product_name, product_path FROM product ORDER BY product_createat DESC ");
  $stmtPop->execute();
  $resultPop = $stmtPop->get_result();
  $stmtPop->close();

  // แสดงรายการ Random
  $stmtRnd = $mysqli->prepare("SELECT product_id, product_name, product_path FROM product ORDER BY RAND() ");
  $stmtRnd->execute();
  $resultRnd = $stmtRnd->get_result();
  $stmtRnd->close();
}

// เตรียม query string สำหรับลิงก์
$carry = array();
if ($cat !== '') {
  $carry['cat'] = $cat;
}
if ($q !== '') {
  $carry['q'] = $q;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pixora | Main</title>
  <link rel="stylesheet" href="../css/StyleMain5.css">
</head>

<body>
  <header class="site-header">
    <div class="topnav">
      <a href="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" class="brand" aria-label="Pixora Home">
        <h1>Pixora</h1>
      </a>

      <div class="search-container">
        <form class="search-box" method="get" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" role="search">
          <?php if ($cat !== ''): ?>
            <input type="hidden" name="cat" value="<?php echo htmlspecialchars($cat); ?>">
          <?php endif; ?>
          <input type="text" class="search-input" name="q" placeholder="Search images..." value="<?php echo htmlspecialchars($q); ?>">
          <button type="submit" class="search-btn">Search</button>
        </form>
      </div>

      <div class="top-actions">
        <a href="cart.php" class="icon-btn" title="Cart">
          <img src="./picture and video/shopping-cart.png" alt="cart">
        </a>
        <a href="addimage.php" class="icon-btn" title="Add image">
          <img src="./picture and video/addimage.png" alt="add image">
        </a>
        <a href="user_gallery.php" class="icon-btn" title="Gallery">
          <img src="./picture and video/photo.png" alt="gallery">
        </a>
        <a href="user_product.php" class="icon-btn" title="Product">
          <img src="./picture and video/product.png" alt="product">
        </a>

        <div class="profile-dropdown">
          <div class="icon-btn profile-toggle" title="Profile">
            <img src="<?php echo $picSrc; ?>" alt="Profile" width="42" height="42"
              style="border-radius:50%; object-fit:cover; border:2px solid rgba(0,0,0,.06); box-shadow:0 2px 8px rgba(0,0,0,.12);"
              onerror="this.src='<?php echo $defaultAvatar; ?>';">
          </div>
          <div class="dropdown-menu">
            <ul>
              <li>
                <a href="editProfile.php">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                  </svg>
                  Profile Settings
                </a>
              </li>
              <li>
                <a href="logout.php" class="logout-link">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  Logout
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </header>

  <main class="layout">
    <aside class="sidebar">
      <ul>
        <?php
        $base = array();
        if ($q !== '') {
          $base['q'] = $q;
        }
        ?>
        <li><a href="<?php echo buildUrl($base); ?>" <?php echo activeAttr($cat, 'ALL'); ?>>All</a></li>
        <li><a href="<?php echo buildUrl(array_merge($base, array('cat' => 'Art & Design'))); ?>" <?php echo activeAttr($cat, 'Art & Design'); ?>>Art & Design</a></li>
        <li><a href="<?php echo buildUrl(array_merge($base, array('cat' => 'Health & Fitness'))); ?>" <?php echo activeAttr($cat, 'Health & Fitness'); ?>>Health & Fitness</a></li>
        <li><a href="<?php echo buildUrl(array_merge($base, array('cat' => 'Technology & Business'))); ?>" <?php echo activeAttr($cat, 'Technology & Business'); ?>>Technology & Business</a></li>
        <li><a href="<?php echo buildUrl(array_merge($base, array('cat' => 'Travel & Adventure'))); ?>" <?php echo activeAttr($cat, 'Travel & Adventure'); ?>>Travel & Adventure</a></li>
        <li><a href="<?php echo buildUrl(array_merge($base, array('cat' => 'Food & Drink'))); ?>" <?php echo activeAttr($cat, 'Food & Drink'); ?>>Food & Drink</a></li>
      </ul>
    </aside>

    <div class="content">
      <?php if (count($filters) > 0): ?>
        <!-- แสดงผลการค้นหา -->
        <section class="block">
          <h2>
            Results for
            <?php if ($cat): echo htmlspecialchars($cat) . ' ';
            endif; ?>
            <?php if ($q): echo '"' . htmlspecialchars($q) . '"';
            endif; ?>
          </h2>
          <div class="card-grid">
            <?php if ($resultFiltered && $resultFiltered->num_rows > 0): ?>
              <?php while ($r = $resultFiltered->fetch_assoc()): ?>
                <?php
                $file = basename($r['product_path']);
                $img = $productDirUrl . rawurlencode($file);
                $linkParams = array_merge($carry, array('id' => $r['product_id']));
                $link = 'product.php?' . http_build_query($linkParams);
                ?>
                <a class="card" href="<?php echo $link; ?>">
                  <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($r['product_name']); ?>">
                  <h3 class="card-title"><?php echo htmlspecialchars($r['product_name']); ?></h3>
                </a>
              <?php endwhile; ?>
            <?php else: ?>
              <p>No results found.</p>
            <?php endif; ?>
          </div>
        </section>
      <?php else: ?>
        <!-- แสดง Popular Today -->
        <section class="block">
          <h2>Popular Today</h2>
          <div class="card-grid">
            <?php if ($resultPop && $resultPop->num_rows > 0): ?>
              <?php while ($r = $resultPop->fetch_assoc()): ?>
                <?php
                $file = basename($r['product_path']);
                $img = $productDirUrl . rawurlencode($file);
                $linkParams = array_merge($carry, array('id' => $r['product_id']));
                $link = 'product.php?' . http_build_query($linkParams);
                ?>
                <a class="card" href="<?php echo $link; ?>">
                  <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($r['product_name']); ?>">
                  <h3 class="card-title"><?php echo htmlspecialchars($r['product_name']); ?></h3>
                </a>
              <?php endwhile; ?>
            <?php else: ?>
              <p>No popular items yet.</p>
            <?php endif; ?>
          </div>
        </section>

        <!-- แสดง Random Picture -->
        <section class="block">
          <h2>Random Picture</h2>
          <div class="card-grid">
            <?php if ($resultRnd && $resultRnd->num_rows > 0): ?>
              <?php while ($r = $resultRnd->fetch_assoc()): ?>
                <?php
                $file = basename($r['product_path']);
                $img = $productDirUrl . rawurlencode($file);
                $linkParams = array_merge($carry, array('id' => $r['product_id']));
                $link = 'product.php?' . http_build_query($linkParams);
                ?>
                <a class="card" href="<?php echo $link; ?>">
                  <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($r['product_name']); ?>">
                  <h3 class="card-title"><?php echo htmlspecialchars($r['product_name']); ?></h3>
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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var dropdown = document.querySelector('.profile-dropdown');
      var toggle = document.querySelector('.profile-toggle');

      if (toggle) {
        toggle.addEventListener('click', function(e) {
          e.stopPropagation();
          dropdown.classList.toggle('active');
        });
      }

      document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target)) {
          dropdown.classList.remove('active');
        }
      });
    });
  </script>
</body>

</html>