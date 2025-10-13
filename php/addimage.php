<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Addimage</title>
    <link rel="stylesheet" href="../css/StyleAddimg.css">
</head>
<body>
    <?php
session_start();
require 'conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_POST['upload'])) {
    $title = $_POST['product_name'];
    $description = $_POST['product_description'];
    $price = $_POST['product_price'];
    $category = $_POST['categories_name'];
    $user_id = $_SESSION['user_id'];

    $targetDir = "../image_product/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    $filename = basename($_FILES['product_path']['name']);
    $tempname = $_FILES['product_path']['tmp_name'];
    $targetFile = $targetDir . $filename;

    if (move_uploaded_file($tempname, $targetFile)) {

        // INSERT ลง product
        $sql_insert_product = "INSERT INTO product (creator_id, categories_name, product_name, product_description, product_path, product_price)
                               VALUES ('$user_id', '$category', '$title', '$description', '$filename', '$price')";

        if (mysqli_query($mysqli, $sql_insert_product)) {
            $product_id = mysqli_insert_id($mysqli); // id ของ product ที่เพิ่ง insert

            // INSERT ลง user_product
            $sql_user_product = "INSERT INTO user_product (up_iduser, up_idproduct) 
                                 VALUES ('$user_id', '$product_id')";
            if (mysqli_query($mysqli, $sql_user_product)) {
                echo "<script>alert('✅ Uploaded successfully!');</script>";
            } else {
                echo "<script>alert('❌ Failed to link user and product: " . mysqli_error($mysqli) . "');</script>";
            }

        } else {
            echo "<script>alert('❌ Failed to insert product: " . mysqli_error($mysqli) . "');</script>";
        }

    } else {
        echo "<script>alert('❌ Upload Failed');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Image - Pixora</title>
    <link rel="stylesheet" href="../css/Style_AddImage.css">
</head>
<body>
<header>
    <nav class="navbar">
        <h1>Pixora</h1>
    </nav>
</header>

<section class="add-image-container">
    <h2>Upload Your Image</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Image File:</label>
            <input type="file" name="product_path" accept="image/*" required>
        </div>

        <div class="form-group">
            <label>Title:</label>
            <input type="text" name="product_name" placeholder="Enter image title" required>
        </div>

        <div class="form-group">
            <label>Description:</label>
            <textarea name="product_description" placeholder="Write a short description..." rows="4" required></textarea>
        </div>

        <div class="form-group">
            <label>Price (฿):</label>
            <input type="number" name="product_price" min="0" step="0.01" required>
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

<footer>
    <p>© 2025 Pixora. All rights reserved.</p>
</footer>
</body>
</html>