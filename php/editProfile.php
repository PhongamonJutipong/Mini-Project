<?php
$db = mysqli_connect("localhost", "root", "", "picture_store");

if (!$db) {
    die("Database connection failed: " . mysqli_connect_error());
}

$user_id = 1; // สมมติเป็น user ที่ล็อกอินอยู่

if (isset($_POST['edit_profile'])) {
    $user_name = $_POST['user_name'];
    $user_email = $_POST['user_email'];
    $user_tel = $_POST['user_tel'];

    $filename = $_FILES['user_picture']['name'];
    $tempname = $_FILES['user_picture']['tmp_name'];
    $folder = "image_user/" . basename($filename);

    // อัปโหลดไฟล์
    if (move_uploaded_file($tempname, $folder)) {
        $sql = "UPDATE user 
                SET user_name='$user_name',
                    user_email='$user_email',
                    user_tel='$user_tel',
                    user_picture='$filename'
                WHERE user_id=$user_id";
    } else {
        // ถ้าไม่อัปโหลดรูปใหม่ จะไม่เปลี่ยนรูปในฐานข้อมูล
        $sql = "UPDATE user 
                SET user_name='$user_name',
                    user_email='$user_email',
                    user_tel='$user_tel'
                WHERE user_id=$user_id";
    }

    if (mysqli_query($db, $sql)) {
        echo "<script>alert('Profile updated successfully');</script>";
    } else {
        echo "<script>alert('Database update failed: " . mysqli_error($db) . "');</script>";
    }
}

// ดึงข้อมูลผู้ใช้มาแสดง
$result = mysqli_query($db, "SELECT * FROM user WHERE user_id=$user_id");
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="Style_Profile.css">
    <title>Profile</title>
</head>
<body>
    <h2>Profile</h2>
    <div class="profile-container">
        <h3>Edit Profile</h3>
        <form action="" method="post" enctype="multipart/form-data">
            <img src="<?php echo 'image_user/' . $user['user_picture']; ?>" alt="Profile Picture" class="dp"><br><br>

            <label for="user_picture">Profile Image:</label>
            <input type="file" id="user_picture" name="user_picture"><br><br>

            <label for="user_name">Username:</label>
            <input type="text" id="user_name" name="user_name" value="<?php echo $user['user_name']; ?>" required><br><br>

            <label for="user_email">Email:</label>
            <input type="email" id="user_email" name="user_email" value="<?php echo $user['user_email']; ?>" required><br><br>

            <label for="user_tel">Phone Number:</label>
            <input type="tel" id="user_tel" name="user_tel" value="<?php echo $user['user_tel']; ?>" required><br><br>

            <button type="submit" name="edit_profile">Save Changes</button>
        </form>
    </div>
</body>
</html>
