<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="StyleProfile.css">
    <title>Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <header>
            <nav>
                <div class="navbar-profile">
                    <h1>Pixora</h1>
                    <div class="nav-3">
                        <a href="">Home</a>
                        <a href="">Gallery</a>
                        <a href="">Card</a>
                    </div>
                    <div class="nav-2">
                        <a href="register.html">Register</a>
                        <a href="Login.html">Login</a>
                    </div>
                </div>
            </nav>
        </header>
        <hr>
        <br>
        <br>
        <h2>Profile</h2>
        <div class="profile-container">
        <h3>Edit Profile</h3>
        <form action="edit_profile_success" method="post">
            <label for="image">Image:</label>
            <input type="file" id="image" name="image" required><br><br>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required><br><br>

            <button type="submit">Save Changes</button>
    </form>
    </div>
    <br>
    <br> 
    <footer>
        <p>© 2025 Pixora. All rights reserved.</p>
    </footer>
</body>
</html>