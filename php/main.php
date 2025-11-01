<?php
session_start();
require __DIR__ . '/conn.php';

// Path
$base = dirname(dirname($_SERVER['SCRIPT_NAME']));
$base = ($base === '/') ? '' : rtrim($base, '/');
$imgUrl = $base . '/php/image_user/';
$prodUrl = $base . '/php/image_product/';

// รูปโปรไฟล์
$pic = $_SESSION['user_picture'] ?? null;
if (!$pic && $uid = (int)$_SESSION['user_id']) {
  $row = $mysqli->query("SELECT user_picture FROM user WHERE user_id = $uid")->fetch_assoc();
  $pic = $_SESSION['user_picture'] = $row['user_picture'] ?? null;
}
$picSrc = $pic ? (strpos($pic, 'http') === 0 ? $pic : $imgUrl . basename($pic)) : $base . '/php/image_user/account.png';

// รับค่าจาก URL
$q = $_GET['q'] ?? '';
$cat = $_GET['cat'] ?? '';

// Query
$sql = "SELECT product_id, product_name, product_path FROM product WHERE 1=1";
$params = [];
$types = '';

//เลือกหมวดหมู่
if ($cat) {
  $sql .= " AND categories_name = ?";
  $params[] = $cat;
  $types .= 's';
}

//ถ้ามีการค้นหา
if ($q) {
  $sql .= " AND product_name LIKE ?";
  $params[] = "%$q%";
  $types .= 's';
}

//(มี category หรือ search)
if ($cat || $q) {
  $sql .= " ORDER BY product_createat DESC LIMIT 24";
  $stmt = $mysqli->prepare($sql);
  if ($types) $stmt->bind_param($types, ...$params);
  $stmt->execute();
  $result = $stmt->get_result();
  $stmt->close();
} else {
  $stmt1 = $mysqli->query("SELECT product_id, product_name, product_path FROM product ORDER BY product_createat DESC");
  $stmt2 = $mysqli->query("SELECT product_id, product_name, product_path FROM product ORDER BY RAND()");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pixora Main</title>
  <link rel="stylesheet" href="../css/StyleMain5.css">
</head>

<body>
  <header class="site-header">
    <div class="topnav">
      <a href="<?= $_SERVER['PHP_SELF'] ?>" class="brand" aria-label="Pixora Home">
        <h1>Pixora</h1>
      </a>

      <div class="search-container">
        <form class="search-box" method="get" action="<?= $_SERVER['PHP_SELF'] ?>" role="search">
          <?php if ($cat): ?><input type="hidden" name="cat" value="<?= $cat ?>"><?php endif; ?>
          <input type="text" class="search-input" name="q" placeholder="Search images..." value="<?= $q ?>">
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
            <img src="<?= $picSrc ?>" alt="Profile" width="42" height="42"
              style="border-radius:50%; object-fit:cover; border:2px solid rgba(0,0,0,.06); box-shadow:0 2px 8px rgba(0,0,0,.12);">
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
        <li><a href="?<?= $q ? 'q=' . urlencode($q) : '' ?>" <?= !$cat ? 'aria-current="page" class="active"' : '' ?>>All</a></li>
        <li><a href="?cat=<?= urlencode('Art & Design') ?><?= $q ? '&q=' . urlencode($q) : '' ?>" <?= $cat == 'Art & Design' ? 'aria-current="page" class="active"' : '' ?>>Art & Design</a></li>
        <li><a href="?cat=<?= urlencode('Health & Fitness') ?><?= $q ? '&q=' . urlencode($q) : '' ?>" <?= $cat == 'Health & Fitness' ? 'aria-current="page" class="active"' : '' ?>>Health & Fitness</a></li>
        <li><a href="?cat=<?= urlencode('Technology & Business') ?><?= $q ? '&q=' . urlencode($q) : '' ?>" <?= $cat == 'Technology & Business' ? 'aria-current="page" class="active"' : '' ?>>Technology & Business</a></li>
        <li><a href="?cat=<?= urlencode('Travel & Adventure') ?><?= $q ? '&q=' . urlencode($q) : '' ?>" <?= $cat == 'Travel & Adventure' ? 'aria-current="page" class="active"' : '' ?>>Travel & Adventure</a></li>
        <li><a href="?cat=<?= urlencode('Food & Drink') ?><?= $q ? '&q=' . urlencode($q) : '' ?>" <?= $cat == 'Food & Drink' ? 'aria-current="page" class="active"' : '' ?>>Food & Drink</a></li>
      </ul>
    </aside>

    <div class="content">
      <?php if ($cat || $q): ?> 
        <section class="block">
          <h2>
            Results for
            <?php if ($cat): echo $cat . ' ';
            endif; ?>
            <?php if ($q): echo '"' . $q . '"';
            endif; ?>
          </h2>
          <div class="card-grid">
            <?php if ($result && $result->num_rows > 0): ?><!-- เช็คว่ามีผลลัพธ์หรือไม่ -->
              <?php while ($r = $result->fetch_assoc()): ?> <!-- ดึงข้อมูลแถวถัดไป -->
                <a class="card" href="product.php?id=<?= $r['product_id'] ?><?= $cat ? '&cat=' . urlencode($cat) : '' ?><?= $q ? '&q=' . urlencode($q) : '' ?>">
                  <img src="<?= $prodUrl . basename($r['product_path']) ?>" alt="<?= $r['product_name'] ?>">
                  <h3 class="card-title"><?= $r['product_name'] ?></h3>
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
            <?php if ($stmt1 && $stmt1->num_rows > 0): ?>
              <?php while ($r = $stmt1->fetch_assoc()): ?>
                <a class="card" href="product.php?id=<?= $r['product_id'] ?>">
                  <img src="<?= $prodUrl . basename($r['product_path']) ?>" alt="<?= $r['product_name'] ?>">
                  <h3 class="card-title"><?= $r['product_name'] ?></h3>
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
            <?php if ($stmt2 && $stmt2->num_rows > 0): ?>
              <?php while ($r = $stmt2->fetch_assoc()): ?>
                <a class="card" href="product.php?id=<?= $r['product_id'] ?>">
                  <img src="<?= $prodUrl . basename($r['product_path']) ?>" alt="<?= $r['product_name'] ?>">
                  <h3 class="card-title"><?= $r['product_name'] ?></h3>
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