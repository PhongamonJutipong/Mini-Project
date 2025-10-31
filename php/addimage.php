<?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_POST['upload'])) {
    $title = mysqli_real_escape_string($mysqli, $_POST['product_name']);
    $description = mysqli_real_escape_string($mysqli, $_POST['product_description']);
    $price = mysqli_real_escape_string($mysqli, $_POST['product_price']);
    $category = mysqli_real_escape_string($mysqli, $_POST['categories_name']);
    $user_id = $_SESSION['user_id'];

    // ⭐ แก้ path ให้ตรงกับโฟลเดอร์จริง (เหมือนใน index.php)
    $targetDir = __DIR__ . "/image_product/";  // อยู่ใน /php/image_product/
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filename = basename($_FILES['product_path']['name']);
    $tempname = $_FILES['product_path']['tmp_name'];
    $targetFile = $targetDir . $filename;

    // ตรวจสอบว่าเป็นไฟล์รูปจริงๆ
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (!in_array($imageFileType, $allowedTypes)) {
        echo "<script>alert('Only JPG, JPEG, PNG, GIF & WEBP files are allowed!'); history.back();</script>";
        exit();
    } elseif ($_FILES['product_path']['size'] > 5000000) { // 5MB
        echo "<script>alert('File is too large! Maximum file size is 5MB.'); history.back();</script>";
        exit();
    } elseif (move_uploaded_file($tempname, $targetFile)) {

        // ⭐ บันทึกเฉพาะชื่อไฟล์ (ไม่ใช่ path เต็ม)
        $sql_insert_product = "INSERT INTO product (creator_id, categories_name, product_name, product_description, product_path, product_price, product_createat)
                               VALUES (?, ?, ?, ?, ?, ?, NOW())";

        $stmt = $mysqli->prepare($sql_insert_product);
        $stmt->bind_param("issssd", $user_id, $category, $title, $description, $filename, $price);

        if ($stmt->execute()) {
            $product_id = $stmt->insert_id;

            // INSERT ลง user_product
            $sql_user_product = "INSERT INTO user_product (up_iduser, up_idproduct) 
                                 VALUES (?, ?)";
            $stmt2 = $mysqli->prepare($sql_user_product);
            $stmt2->bind_param("ii", $user_id, $product_id);

            if ($stmt2->execute()) {
                echo "<script>
                    alert('Image uploaded successfully!');
                    window.location.href = 'main.php';
                </script>";
                exit();
            } else {
                echo "<script>alert('Failed to link user and product: " . addslashes($stmt2->error) . "'); history.back();</script>";
                exit();
            }
            $stmt2->close();
        } else {
            echo "<script>alert('Failed to insert product: " . addslashes($stmt->error) . "'); history.back();</script>";
            exit();
        }
        $stmt->close();
    } else {
        echo "<script>alert('Upload failed. Please check folder permissions.'); history.back();</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Image - Pixora</title>
    <link rel="stylesheet" href="../css/StyleAddimg2.css">
</head>

<body>
    <header>
        <nav class="navbar">
            <a href="main.php" class="brand" style="text-decoration: none;">
                <h1>Pixora</h1>
            </a>
        </nav>
    </header>

    <section class="add-image-container">
        <h2>Upload Your Image</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Image File:</label>
                <input type="file" name="product_path" accept="image/*" required>
                <small>Max file size: 5MB (JPG, PNG, GIF, WEBP)</small>
            </div>

            <div class="form-group">
                <label>Title:</label>
                <input type="text" name="product_name" placeholder="Enter image title" maxlength="200" required>
            </div>

            <div class="form-group">
                <label>Description:</label>
                <textarea name="product_description" placeholder="Write a short description..." rows="4" maxlength="1000" required></textarea>
            </div>

            <div class="form-group">
                <label>Price (฿):</label>
                <input type="number" name="product_price" min="0" step="0.01" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label>Category:</label>
                <select name="categories_name" required>
                    <option value="">-- Select Category --</option>
                    <option value="Art & Design">Art & Design</option>
                    <option value="Health & Fitness">Health & Fitness</option>
                    <option value="Technology & Business">Technology & Business</option>
                    <option value="Travel & Adventure">Travel & Adventure</option>
                    <option value="Food & Drink">Food & Drink</option>
                </select>
            </div>

            <button type="submit" name="upload" class="upload-btn">Upload Image</button>
        </form>
    </section>
</body>

</html>