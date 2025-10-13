<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก</title>
    <link rel="stylesheet" href="..//css//StyleRegister.css">

    <script>
        function validateForm(event) {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const checkbox = document.getElementById("terms_checkbox");
            if (password !== confirmPassword) {
                alert("Passwords do not match. Please try again.");
                event.preventDefault();
                return false;
            }
            if (!checkbox.checked) {
                alert("Please accept the Terms and Conditions and Privacy Policy before registering.");
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</head>

<body>
    <div class="register-container">
        <h2>Register</h2>
        <form action="registerSuccess.php" method="post" enctype="multipart/form-data" onsubmit="return validateForm(event)">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required><br><br>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" placeholder="e.g., example@example.com" required><br><br>

            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required><br><br>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required><br><br>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required><br><br>

            <label>
                <input type="checkbox" id="terms_checkbox">
                Accept <a href="term.php" target="_blank">Terms and Conditions</a> and
                <a href="privacy.php" target="_blank">Privacy Policy</a>
            </label>
            <br><br>

            <button type="submit">Register</button>

        </form>

        <br><br>
        <hr>
        <a href="Login.php">If you already have an account, click here</a>
    </div>
</body>

</html>