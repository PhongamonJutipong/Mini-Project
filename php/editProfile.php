<?php
session_start();
$db = mysqli_connect("localhost", "root", "", "picture_store");
if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['user_id'])) {
    die("Please sign in before editing your profile.");
}
$user_id = (int)$_SESSION['user_id'];

/* ==== base URL ให้ตรงกับ main.php ==== */
$projectUrlBase = dirname(dirname($_SERVER['SCRIPT_NAME'])); // e.g., /Mini Project
if ($projectUrlBase === DIRECTORY_SEPARATOR) $projectUrlBase = '';
$projectUrlBaseSafe = str_replace(' ', '%20', $projectUrlBase);

/* ==== โฟลเดอร์รูปโปรไฟล์ (FS + URL) ==== */
$uploadFsDir  = __DIR__ . '/image_user/';                    // {PROJECT}/php/image_user/
$uploadUrlDir = $projectUrlBaseSafe . '/php/image_user/';    // /Mini%20Project/php/image_user/
if (!is_dir($uploadFsDir)) {
    @mkdir($uploadFsDir, 0777, true);
}

/* default avatar (ปรับ path ถ้าจำเป็น) */
$defaultAvatar = $projectUrlBaseSafe . '/php/assets/default-avatar.png';

if (isset($_POST['edit_profile'])) {
    $user_name  = trim($_POST['user_name'] ?? '');
    $user_email = trim($_POST['user_email'] ?? '');
    $user_tel   = trim($_POST['user_tel'] ?? '');

    $newFile = null;
    if (!empty($_FILES['user_picture']['name']) && is_uploaded_file($_FILES['user_picture']['tmp_name'])) {
        $origName = $_FILES['user_picture']['name'];
        $tmpPath  = $_FILES['user_picture']['tmp_name'];

        // Validate extension + generate a new filename
        $ext = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            echo "<script>alert('Only image files are allowed: jpg, jpeg, png, gif, webp.');</script>";
        } else {
            $safeBase = preg_replace('~[^a-zA-Z0-9._-]+~', '-', pathinfo($origName, PATHINFO_FILENAME));
            $newFile  = $safeBase . '-' . time() . '.' . $ext;

            if (!move_uploaded_file($tmpPath, $uploadFsDir . $newFile)) {
                $newFile = null;
                echo "<script>alert('File upload failed.');</script>";
            }
        }
    }

    // UPDATE with prepared statement
    if ($newFile) {
        $stmt = $db->prepare("UPDATE user SET user_name=?, user_email=?, user_tel=?, user_picture=? WHERE user_id=?");
        $stmt->bind_param("ssssi", $user_name, $user_email, $user_tel, $newFile, $user_id);
    } else {
        $stmt = $db->prepare("UPDATE user SET user_name=?, user_email=?, user_tel=? WHERE user_id=?");
        $stmt->bind_param("sssi", $user_name, $user_email, $user_tel, $user_id);
    }

    if ($stmt && $stmt->execute()) {
        if ($newFile) {
            // Update session so main.php sees the new picture
            $_SESSION['user_picture'] = $newFile;
        }
        // Reload to prevent form resubmission + refresh cache
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        echo "<script>alert('Database update failed. Please try again later.');</script>";
    }
    if ($stmt) $stmt->close();
}

/* Fetch user data */
$stmt2 = $db->prepare("SELECT user_name, user_email, user_tel, user_picture FROM user WHERE user_id=?");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$stmt2->bind_result($u_name, $u_email, $u_tel, $u_pic);
$stmt2->fetch();
$stmt2->close();

/* Prepare picture URL + cache-buster */
$picUrl = $defaultAvatar;
if (!empty($u_pic)) {
    $fs = $uploadFsDir . basename($u_pic);
    if (is_file($fs)) {
        $picUrl = $uploadUrlDir . rawurlencode(basename($u_pic)) . '?v=' . filemtime($fs);
    }
}
?>
<!DOCTYPE html>
<html lang="en"> <!-- เปลี่ยนเป็น en ให้สอดคล้องกับข้อความ -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/Style_Profile2.css">
    <title>Edit Profile</title>
</head>

<body>
    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="main.php" class="brand">
            <h1>Pixora</h1>
        </a>
    </nav>

    <!-- PROFILE CONTAINER -->
    <div class="profile-container">
        <h2>Edit Profile</h2>

        <form action="editprofilesuccess.php" method="post" enctype="multipart/form-data">
            <div class="profile-image-wrapper">
                <img src="<?= htmlspecialchars($picUrl) ?>" alt="Profile Picture" class="dp"
                    onerror="this.onerror=null; this.src='<?= htmlspecialchars($defaultAvatar) ?>'">
            </div>

            <div class="form-group">
                <label for="user_picture">Profile Image:</label>
                <input type="file" id="user_picture" name="user_picture" accept=".jpg,.jpeg,.png,.gif,.webp">
            </div>

            <div class="form-group">
                <label for="user_name">Username:</label>
                <input type="text" id="user_name" name="user_name" value="<?= htmlspecialchars($u_name) ?>" required>
            </div>

            <div class="form-group">
                <label for="user_email">Email:</label>
                <input type="email" id="user_email" name="user_email" value="<?= htmlspecialchars($u_email) ?>" required>
            </div>

            <div class="form-group">
                <label for="user_tel">Phone Number:</label>
                <input type="tel" id="user_tel" name="user_tel" value="<?= htmlspecialchars($u_tel) ?>" required>
            </div>

            <button type="submit" name="edit_profile">Save Changes</button>
        </form>
    </div>
</body>

</html>