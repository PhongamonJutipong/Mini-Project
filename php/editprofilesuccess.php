<?php
session_start();

/* -------- 0) Require login -------- */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* -------- 1) DB connect -------- */
$db = mysqli_connect("localhost", "root", "", "picture_store");
if (!$db) {
    http_response_code(500);
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = (int)$_SESSION['user_id'];

/* -------- 2) Path base (ให้สอดคล้องกับโปรเจกต์) -------- */
$projectUrlBase = dirname(dirname($_SERVER['SCRIPT_NAME'])); // เช่น /Mini Project
if ($projectUrlBase === DIRECTORY_SEPARATOR) $projectUrlBase = '';
$projectUrlBaseSafe = str_replace(' ', '%20', $projectUrlBase);

$uploadFsDir  = __DIR__ . '/image_user/';                 // {PROJECT}/php/image_user/
$uploadUrlDir = $projectUrlBaseSafe . '/php/image_user/'; // /Mini%20Project/php/image_user/
if (!is_dir($uploadFsDir)) { @mkdir($uploadFsDir, 0777, true); }

$defaultAvatar = $projectUrlBaseSafe . '/php/assets/default-avatar.png';

/* -------- 3) ถ้าเข้าหน้านี้โดยไม่ POST ให้เด้งกลับ -------- */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['edit_profile'])) {
    header("Location: editProfile.php");
    exit;
}

/* -------- 4) รับค่าและตรวจสอบ -------- */
$user_name  = trim($_POST['user_name'] ?? '');
$user_email = trim($_POST['user_email'] ?? '');
$user_tel   = trim($_POST['user_tel'] ?? '');

$err = null;
if ($user_name === '' || $user_email === '' || $user_tel === '') {
    $err = 'กรุณากรอกข้อมูลให้ครบถ้วน';
} elseif (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
    $err = 'รูปแบบอีเมลไม่ถูกต้อง';
}

/* -------- 5) จัดการไฟล์อัปโหลด (ถ้ามี) -------- */
$newFile = null;
if (!$err && !empty($_FILES['user_picture']['name']) && is_uploaded_file($_FILES['user_picture']['tmp_name'])) {
    $origName = $_FILES['user_picture']['name'];
    $tmpPath  = $_FILES['user_picture']['tmp_name'];
    $ext      = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
    $allowed  = ['jpg','jpeg','png','gif','webp'];

    if (!in_array($ext, $allowed, true)) {
        $err = 'อนุญาตเฉพาะไฟล์ภาพ: jpg, jpeg, png, gif, webp';
    } else {
        $safeBase = preg_replace('~[^a-zA-Z0-9._-]+~', '-', pathinfo($origName, PATHINFO_FILENAME));
        $newFile  = $safeBase . '-' . time() . '.' . $ext;
        if (!move_uploaded_file($tmpPath, $uploadFsDir . $newFile)) {
            $newFile = null;
            $err = 'อัปโหลดไฟล์ไม่สำเร็จ';
        }
    }
}

/* -------- 6) UPDATE DB -------- */
$updated = false;
if (!$err) {
    if ($newFile) {
        $stmt = $db->prepare("UPDATE user SET user_name=?, user_email=?, user_tel=?, user_picture=? WHERE user_id=?");
        $stmt->bind_param("ssssi", $user_name, $user_email, $user_tel, $newFile, $user_id);
    } else {
        $stmt = $db->prepare("UPDATE user SET user_name=?, user_email=?, user_tel=? WHERE user_id=?");
        $stmt->bind_param("sssi", $user_name, $user_email, $user_tel, $user_id);
    }

    if ($stmt && $stmt->execute()) {
        $updated = true;
        // sync session ให้หน้าอื่นเห็นค่าล่าสุด
        $_SESSION['user_name']  = $user_name;
        $_SESSION['user_email'] = $user_email;
        $_SESSION['user_tel']   = $user_tel;
        if ($newFile) {
            $_SESSION['user_picture'] = $newFile;
        }
    } else {
        if ($db->errno === 1062) {
            $err = 'อีเมลนี้ถูกใช้แล้ว เลือกอีเมลอื่น';
        } else {
            $err = 'Database update failed: ' . $db->error;
        }
    }
    if ($stmt) { $stmt->close(); }
}

/* -------- 7) เตรียมข้อมูลสำหรับแสดงผล -------- */
$displayName = $_SESSION['user_name'] ?? 'User';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="../css/Styleeditprofilesuccess.css">
    <title><?= $updated && !$err ? 'Update Success' : 'Update Failed' ?></title>
</head>
<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="main.php" class="brand"><h1>Picture Store</h1></a>
        <div class="nav-links">
            <a href="main.php">Home</a>
            <a href="gallery.php">Gallery</a>
            <a href="profile.php">Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <!-- CONTAINER -->
    <div class="success-container">
        <?php if ($updated && !$err): ?>
            <div class="success-icon">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#D22B2B" stroke-width="4" class="circle-animation"/>
                    <path d="M 25 50 L 40 65 L 75 30" fill="none" stroke="#D22B2B" stroke-width="5" stroke-linecap="round" stroke-linejoin="round" class="check-animation"/>
                </svg>
            </div>
            <h2>Information updated successfully!</h2>
            <p class="success-message">Hi <?= htmlspecialchars($displayName) ?>, your profile has been updated.</p>

            <div class="button-group">
                <a href="editProfile.php" class="btn btn-primary">Return to profile</a>
                <a href="main.php" class="btn btn-secondary">Go to homepage</a>
            </div>
        <?php else: ?>
            <div class="success-icon">
                <!-- ใช้เครื่องหมายกากบาทแทน หรือจะเปลี่ยนสีเป็นส้ม/แดงก็ได้ -->
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#B00020" stroke-width="4"/>
                    <path d="M 32 32 L 68 68 M 68 32 L 32 68" fill="none" stroke="#B00020" stroke-width="5" stroke-linecap="round"/>
                </svg>
            </div>
            <h2>Update failed</h2>
            <p class="success-message"><?= htmlspecialchars($err ?? 'Unknown error') ?></p>

            <div class="button-group">
                <a href="editProfile.php" class="btn btn-primary">Back to edit</a>
                <a href="main.php" class="btn btn-secondary">Go to homepage</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2025 Picture Store. All rights reserved.</p>
    </footer>
</body>
</html>
