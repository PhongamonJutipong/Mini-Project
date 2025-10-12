<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก</title>
    <link rel="stylesheet" href="StyleRegister.css">

    <script>
        function validateForm(event) {
            const password = document.getElementById("password").value;
            const confirmPassword = document.getElementById("confirm_password").value;
            const checkbox = document.getElementById("terms_checkbox");
            if (password !== confirmPassword) {
                alert("รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน กรุณากรอกใหม่");
                event.preventDefault(); 
                return false;
            }
            if (!checkbox.checked) {
                alert("กรุณายอมรับข้อกำหนดและนโยบายความเป็นส่วนตัวก่อนสมัครสมาชิก");
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="register-container">
        <h2>สมัครสมาชิก</h2>
        <form action="register_success.php" method="post" onsubmit="return validateForm(event)">
           
            <label for="username">ชื่อผู้ใช้:</label>
            <input type="text" id="username" name="username" required><br><br>

            <label for="email">อีเมล:</label>
            <input type="email" id="email" name="email" required><br><br>

            <label for="phone">เบอร์โทรศัพท์:</label>
            <input type="tel" id="phone" name="phone" required><br><br>

            <label for="password">รหัสผ่าน:</label>
            <input type="password" id="password" name="password" required><br><br>

            <label for="confirm_password">ยืนยันรหัสผ่าน:</label>
            <input type="password" id="confirm_password" name="confirm_password" required><br><br>

            <label>
                <input type="checkbox" id="terms_checkbox">
                ฉันยอมรับ <a href="terms.html" target="_blank">ข้อกำหนดและเงื่อนไข</a> และ 
                <a href="privacy.html" target="_blank">นโยบายความเป็นส่วนตัว</a>
            </label>
            <br><br>

            <button type="submit">สมัครสมาชิก</button>

        </form>

        <br><br>
        <hr>
        <a href="Login.html">หากมีบัญชีแล้ว</a>
    </div>
</body>
</html>
