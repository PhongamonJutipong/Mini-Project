<?php
session_start();
require __DIR__ . '/conn.php';

/* ----------------------------------------------------
   1) คำนวณฐานโปรเจกต์ (ทั้ง URL และ Filesystem)
   - ไฟล์นี้อยู่:   {PROJECT}/php/main.php
   - โฟลเดอร์รูป:   {PROJECT}/image_user
   - โฟลเดอร์ไอคอน: {PROJECT}/picture_and_video
---------------------------------------------------- */
$projectFsBase  = dirname(__DIR__);                     // = {PROJECT}
$projectUrlBase = dirname(dirname($_SERVER['SCRIPT_NAME'])); // = /{PROJECT}
if ($projectUrlBase === DIRECTORY_SEPARATOR) $projectUrlBase = ''; // ถ้ารันที่ web root

// โฟลเดอร์รูปโปรไฟล์
$imageDirFs  = $projectFsBase . '/image_user';
$imageDirUrl = $projectUrlBase . '/image_user/';

// โฟลเดอร์ไอคอน/ภาพอื่น
$iconDirUrl  = $projectUrlBase . '/picture_and_video/';

// รูป default
$defaultUrl  = $projectUrlBase . '/assets/default-avatar.png';

/* ----------------------------------------------------
   2) โหลดชื่อไฟล์รูปจาก session/DB
---------------------------------------------------- */
$pic = $_SESSION['user_picture'] ?? null;

if (!$pic && !empty($_SESSION['user_id'])) {
  $stmt = $conn->prepare("SELECT user_picture FROM user WHERE user_id = ?");
  $stmt->bind_param("i", $_SESSION['user_id']);
  $stmt->execute();
  $stmt->bind_result($pic);
  $stmt->fetch();
  $stmt->close();
  if ($pic) $_SESSION['user_picture'] = $pic;
}

/* ----------------------------------------------------
   3) สร้าง src อย่างปลอดภัย
---------------------------------------------------- */
if (!empty($pic)) {
  if (preg_match('~^(https?://|/)~', $pic)) {
    $picSrc = $pic; // URL เต็ม/absolute path
  } else {
    $safe   = basename($pic);                            // กัน path traversal
    $fsPath = $imageDirFs . DIRECTORY_SEPARATOR . $safe; // {PROJECT}/image_user/{file}
    $picSrc = (is_file($fsPath)) ? $imageDirUrl . rawurlencode($safe) : $defaultUrl;
  }
} else {
  $picSrc = $defaultUrl;
}

/* DEBUG (เอาออกได้หลังทดสอบ)
echo "<!-- DEBUG: projectUrlBase=$projectUrlBase | pic=$pic | picSrc=$picSrc -->";
*/
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
          <form class="search-box" action="/action_page.php" role="search" aria-label="Site search">
            <input type="text" class="search-input" placeholder="Search..." name="q" aria-label="Search" />
            <button type="submit" class="search-btn">Search</button>
          </form>
        </div>

        <div class="top-actions" aria-label="User actions">
          <img src="<?= htmlspecialchars($iconDirUrl) ?>shopping-cart.png" alt="Shopping cart">
          <img src="<?= htmlspecialchars($iconDirUrl) ?>favorite.png" alt="Favorites">
          <img src="<?= htmlspecialchars($picSrc, ENT_QUOTES) ?>" alt="Profile" width="100" height="100" style="border-radius:50%;object-fit:cover">
        </div>
      </div>
    </header>

    <main class="layout">
      <aside class="sidebar" aria-label="Categories">
        <ul>
          <li><a href="#">Art & Design</a></li>
          <li><a href="#">Health & Fitness</a></li>
          <li><a href="#">Technology & Business</a></li>
          <li><a href="#">Travel & Adventure</a></li>
          <li><a href="#">Food & Drink</a></li>
        </ul>
      </aside>

      <section class="content">
        <section class="block">
          <h2>Popular Today</h2>
          <div class="card-grid">
            <article class="card"><img src="IMG_logo.jpg" alt="Popular image 1" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Popular image 2" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Popular image 3" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Popular image 4" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Popular image 5" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Popular image 6" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Popular image 7" /></article>
          </div>
        </section>

        <section class="block">
          <h2>Random Picture</h2>
          <div class="card-grid">
            <article class="card"><img src="IMG_logo.jpg" alt="Random image 1" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Random image 2" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Random image 3" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Random image 4" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Random image 5" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Random image 6" /></article>
            <article class="card"><img src="IMG_logo.jpg" alt="Random image 7" /></article>
          </div>
        </section>
      </section>
    </main>
  </div>
</body>

</html>