<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="StyleLogin.css">
</head>

<body>
    <div class="login-containner">
        <div class="login-box">
            <form action="LoginSuccess.php" method="post">
                <h2>LOGIN</h2>
                <input type="text" id="username" placeholder="email" name="username" required>
                <input type="password" id="password" placeholder="password" name="password" required>
                <button type="submit" name="login">Login</button>
            </form>
            <p>Don't have account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>

</html>