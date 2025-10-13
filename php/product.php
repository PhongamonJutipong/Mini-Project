<?php
session_start();
require __DIR__ . '/conn.php';

/* 1) Base paths */
$projectFsBase  = dirname(__DIR__);
$projectUrlBase = dirname(dirname($_SERVER['SCRIPT_NAME']));
if ($projectUrlBase === DIRECTORY_SEPARATOR) $projectUrlBase = '';
$imageDirFs    = $projectFsBase . '/image_user';
$imageDirUrl   = $projectUrlBase . '/image_user/';
$productDirFs  = $projectFsBase . '/image_product';
$productDirUrl = $projectUrlBase . '/image_product/';
$iconDirUrl    = $projectUrlBase . '/picture_and_video/';
$defaultUrl    = $projectUrlBase . '/assets/default-avatar.png';

/* 2) Profile pic */
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

/* 3) รับพารามิเตอร์ id (จำเป็น) + cat/q (กลับหน้าเดิม) */
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$backCat = trim($_GET['cat'] ?? '');
$backQ   = trim($_GET['q'] ?? '');
$backParams = array_filter(['cat' => $backCat ?: null, 'q' => $backQ ?: null]);
$backUrl = 'main.php' . (empty($backParams) ? '' : ('?' . http_build_query($backParams)));

/* 4) Query รายละเอียดสินค้า (join ผู้สร้าง) */
if ($id <= 0) {
    http_response_code(400);
    die('Bad request');
}
$stmt = $mysqli->prepare("
  SELECT
    p.product_id, p.product_name, p.product_description, p.product_path,
    p.product_price, p.product_status, p.product_createat, p.categories_name,
    u.user_id  AS creator_id,
    u.user_name AS creator_name
  FROM product p
  LEFT JOIN user u ON u.user_id = p.creator_id
  WHERE p.product_id = ?
  LIMIT 1
");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
$stmt->close();

if (!$product) {
    http_response_code(404);
    die('Product not found');
}

/* 5) เตรียมรูปภาพ */
$fileSafe = basename($product['product_path'] ?? '');
$imgFs    = $productDirFs . '/' . $fileSafe;
$imgUrl   = is_file($imgFs)
    ? $productDirUrl . rawurlencode($fileSafe)
    : $projectUrlBase . '/assets/no-image.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= htmlspecialchars($product['product_name']) ?> | Pixora</title>
    <link rel="stylesheet" href="../css/StyleProduct.css">
</head>

<body>
    <header class="site-header">
        <div class="topnav">
            <a href="main.php" class="brand">
                <h1>Pixora</h1>
            </a>
            <div class="search-container">
                <form class="search-box" method="get" action="main.php" role="search">
                    <?php if ($backCat): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($backCat) ?>"><?php endif; ?>
                    <?php if ($backQ):   ?><input type="hidden" name="q" value="<?= htmlspecialchars($backQ)   ?>"><?php endif; ?>
                    <input type="text" class="search-input" name="q" placeholder="Search images..." value="">
                    <button class="search-btn">Search</button>
                </form>
            </div>
            <div class="top-actions">
                <img src="<?= htmlspecialchars($iconDirUrl) ?>shopping-cart.png" alt="cart">
                <img src="<?= htmlspecialchars($iconDirUrl) ?>favorite.png" alt="fav">
                <img src="<?= htmlspecialchars($picSrc) ?>" alt="Profile" width="38" height="38">
            </div>
        </div>
    </header>

    <main class="layout">
        <aside class="sidebar">
            <ul>
                <li><a href="<?= htmlspecialchars($backUrl) ?>">← Back</a></li>
            </ul>
        </aside>

        <div class="content">
            <section class="block">
                <h2><?= htmlspecialchars($product['product_name']) ?></h2>

                <div class="product-detail">
                    <figure class="product-figure">
                        <img src="<?= htmlspecialchars($imgUrl) ?>" alt="<?= htmlspecialchars($product['product_name']) ?>">
                    </figure>

                    <div class="meta">
                        <div>
                            <span class="pill"><?= htmlspecialchars($product['categories_name']) ?></span>
                            <span class="pill"><?= htmlspecialchars(ucfirst($product['product_status'])) ?></span>
                        </div>

                        <div class="price">
                            ฿<?= number_format((float)$product['product_price'], 2) ?>
                        </div>

                        <?php if (!empty($product['product_description'])): ?>
                            <p><?= nl2br(htmlspecialchars($product['product_description'])) ?></p>
                        <?php else: ?>
                            <p class="muted">No description.</p>
                        <?php endif; ?>

                        <p class="muted">
                            Creator: <?= htmlspecialchars($product['creator_name'] ?? '—') ?>
                            • Created at: <?= htmlspecialchars($product['product_createat']) ?>
                        </p>

                        <div class="actions">
                            <form method="post" action="add_to_cart.php">
                                <input type="hidden" name="product_id" value="<?= (int)$product['product_id'] ?>">
                                <button class="btn">Add to cart</button>
                            </form>
                            <a class="btn" href="<?= htmlspecialchars($backUrl) ?>">Back to list</a>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>

</html>