<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Control · Pixora</title>
    <link rel="stylesheet" href="../css/StyleMainaddmin.css">
</head>

<body>

    <?php
    require 'conn.php';

    // When searching for a user
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
        $user_id = mysqli_real_escape_string($mysqli, $_POST['user_id']);

        // Retrieve user data
        $sql_user = "SELECT user_id, user_name, user_email, user_tel, user_picture 
                     FROM user 
                     WHERE user_id = '$user_id'";
        $result_user = mysqli_query($mysqli, $sql_user);

        if (mysqli_num_rows($result_user) > 0) {
            $user = mysqli_fetch_assoc($result_user);

            // Retrieve user's posts
            $sql_posts = "SELECT product_id, product_name, product_description, 
                                 product_path, product_price, product_createat 
                          FROM product 
                          WHERE creator_id = '$user_id'";
            $result_posts = mysqli_query($mysqli, $sql_posts);
            $posts = mysqli_fetch_all($result_posts, MYSQLI_ASSOC);
        } else {
            $user = null;
            $posts = [];
            echo "<script>alert('No user found in the system.');</script>";
        }
    }
    ?>

    <header class="site-header">
        <div class="topnav">
            <a href="#" class="brand">
                <h1>Pixora Admin</h1>
            </a>

            <a href="logout.php" class="logout-link">
                Logout
            </a>
        </div>
    </header>

    <main class="container">
        <section class="card">
            <h2>Find User by ID</h2>
            <form class="row" method="POST">
                <div class="field">
                    <label for="uid">User ID</label>
                    <input id="uid" name="user_id" type="text" placeholder="e.g., 1, 2, 3" required />
                    <p class="hint">Enter the user's ID to fetch their information.</p>
                </div>
                <button type="submit" class="btn">Search User</button>
            </form>
        </section>

        <?php if (isset($user) && $user): ?>
            <!-- User Profile -->
            <section class="grid-2">
                <div class="card">
                    <div class="card-head">
                        <h2>User Profile</h2>
                        <span class="status status-active">Fetched</span>
                    </div>

                    <form class="stack" method="POST" action="AdminUpdate.php" enctype="multipart/form-data">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">

                        <div class="field">
                            <label>Full Name</label>
                            <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']) ?>" required />
                        </div>

                        <div class="field">
                            <label>Email</label>
                            <input type="email" name="user_email" value="<?= htmlspecialchars($user['user_email']) ?>" required />
                        </div>

                        <div class="field">
                            <label>Phone Number</label>
                            <input type="text" name="user_tel" value="<?= htmlspecialchars($user['user_tel']) ?>" required />
                        </div>

                        <div class="field">
                            <label>Profile Picture</label><br>
                            <img src="./image_user/<?= htmlspecialchars($user['user_picture']) ?>" alt="Profile" width="100">
                            <br><br>
                            <input type="file" name="user_picture" accept="image/*">
                        </div>

                        <div class="actions">
                            <button type="submit" class="btn">Save Changes</button>
                        </div>
                    </form>
                </div>

                <!-- User Posts -->
                <div class="card">
                    <div class="card-head">
                        <h2>User Posts</h2>
                    </div>
                    <div class="posts">
                        <?php if (!empty($posts)): ?>
                            <?php foreach ($posts as $post): ?>
                                <article class="post">
                                    <div class="post-thumb">
                                        <img src="./image_product/<?= htmlspecialchars($post['product_path']) ?>" alt="Post thumbnail" />
                                    </div>
                                    <div class="post-body">
                                        <h3 class="post-title"><?= htmlspecialchars($post['product_name']) ?></h3>
                                        <p class="post-meta">ID: <?= $post['product_id'] ?> · <?= $post['product_createat'] ?></p>
                                        <p class="post-desc"><?= htmlspecialchars($post['product_description']) ?></p>
                                        <p class="post-price">💰 <?= $post['product_price'] ?> THB</p>
                                        <form method="POST" action="AdminDeletePost.php" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                            <input type="hidden" name="product_id" value="<?= $post['product_id'] ?>">
                                            <button type="submit" class="btn btn-danger">🗑 Delete Post</button>
                                        </form>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>This user has no posts.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    </main>

</body>

</html>