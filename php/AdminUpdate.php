<?php
require 'conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $name = mysqli_real_escape_string($mysqli, $_POST['user_name']);
    $email = mysqli_real_escape_string($mysqli, $_POST['user_email']);
    $tel = mysqli_real_escape_string($mysqli, $_POST['user_tel']);

    if (!empty($_FILES['user_picture']['name'])) {
        $fileName = basename($_FILES['user_picture']['name']);
        $targetFile = "../image_user/" . $fileName;

        if (move_uploaded_file($_FILES['user_picture']['tmp_name'], $targetFile)) {
            $sql = "UPDATE user SET 
                    user_name='$name', 
                    user_email='$email', 
                    user_tel='$tel',
                    user_picture='$fileName'
                    WHERE user_id='$user_id'";
        } else {
            die("อัปโหลดรูปไม่สำเร็จ");
        }
    } else {
        $sql = "UPDATE user SET 
                user_name='$name', 
                user_email='$email', 
                user_tel='$tel'
                WHERE user_id='$user_id'";
    }

    if (mysqli_query($mysqli, $sql)) {
        echo "<script>alert('อัปเดตข้อมูลสำเร็จ'); window.location='AdminMain.php';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . mysqli_error($mysqli) . "');</script>";
    }
}
?>
