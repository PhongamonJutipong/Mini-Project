<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="stylesheet" href="../css/StyleLoginAdmin.css">
</head>
<body>

 
  <nav class="navbar">
    <h1 style="color: red;">Pixora</h1>
    <div class="nav-3">
      <a href="index.php">Home</a>
    </div>
    <div class="nav-2">
      <a href="Login.php">Login</a>
    </div>
  </nav>


  <div class="login-container">
    <div class="login-box">
      <form action="LoginSuccess.php" method="post">
        <h2>ADMIN LOGIN</h2>
        <input type="text" id="username" placeholder="email" name="username" required>
        <input type="password" id="password" placeholder="password" name="password" required>
        <button type="submit" name="loginAdmin">Login</button>
      </form>
    </div>
  </div>

</body>
</html>