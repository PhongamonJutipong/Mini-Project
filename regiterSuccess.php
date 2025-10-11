<?php
// ปิดการแสดง error ที่อาจเปิดเผยข้อมูลระบบ (ใน production)
error_reporting(0);

// ✅ เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root"; // เปลี่ยนตามเครื่องคุณ
$password = "";     // รหัสผ่านฐานข้อมูล (เปลี่ยนตามจริง)
$dbname = "registerDatabase";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// ✅ ป้องกัน SQL Injection ด้วย prepared statement
$user = trim($_POST['username']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$pass = $_POST['password'];
$confirm_pass = $_POST['confirm_password'];

// ✅ ตรวจสอบข้อมูลก่อนบันทึก
if (empty($user) || empty($email) || empty($pass)) {
    die("<h3>⚠️ กรุณากรอกข้อมูลให้ครบ</h3>");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("<h3>⚠️ รูปแบบอีเมลไม่ถูกต้อง</h3>");
}

if ($pass !== $confirm_pass) {
    die("<h3>❌ รหัสผ่านไม่ตรงกัน กรุณากลับไปกรอกใหม่</h3>");
}

// ✅ ตรวจสอบว่าอีเมลหรือชื่อผู้ใช้มีอยู่แล้วหรือไม่
$check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
$check->bind_param("ss", $user, $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die("<h3>⚠️ ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้ไปแล้ว</h3>");
}
$check->close();

// ✅ เข้ารหัสรหัสผ่านก่อนเก็บ
$hashed_password = password_hash($pass, PASSWORD_DEFAULT);

// ✅ ตั้งค่า role เริ่มต้นเป็น "user"
$role = "user";

// ✅ บันทึกข้อมูลลงฐานข้อมูล
$sql = "INSERT INTO users (username, email, phone, password, role) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $user, $email, $phone, $hashed_password, $role);

if ($stmt->execute()) {
    echo "<h2>✅ สมัครสมาชิกสำเร็จ!</h2>";
    echo "<p>บัญชีของคุณถูกสร้างเรียบร้อยแล้ว (สิทธิ์: <strong>$role</strong>)</p>";
    echo "<p><a href='Login.html'>ไปที่หน้าเข้าสู่ระบบ</a></p>";
} else {
    echo "<h3>❌ เกิดข้อผิดพลาด: " . $conn->error . "</h3>";
}

$stmt->close();
$conn->close();
?>