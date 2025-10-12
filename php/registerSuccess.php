<?php
session_start();
require 'conn.php';
$db = $mysqli; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: register.html'); exit(); }

$name     = trim($_POST['user_name']      ?? $_POST['username'] ?? '');
$email    = trim($_POST['user_email']     ?? $_POST['email']    ?? '');
$phone    = trim($_POST['user_tel']       ?? $_POST['phone']    ?? '');
$password =        $_POST['user_password']?? $_POST['password'] ?? '';
$confirm  =        $_POST['confirm_password'] ?? '';

if ($name === '' || $email === '' || $phone === '' || $password === '') {
  $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วน'; header('Location: register.html'); exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $_SESSION['error'] = 'รูปแบบอีเมลไม่ถูกต้อง'; header('Location: register.html'); exit();
}
if ($confirm !== '' && $password !== $confirm) {
  $_SESSION['error'] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน'; header('Location: register.html'); exit();
}

$chk = $db->prepare('SELECT 1 FROM `user` WHERE user_email = ? LIMIT 1');
if (!$chk) { $_SESSION['error'] = 'DB error: prepare(check)'; header('Location: register.html'); exit(); }
$chk->bind_param('s', $email);
$chk->execute();
$chk->store_result();
if ($chk->num_rows > 0) {
  $chk->close();
  $_SESSION['error'] = 'Email นี้ถูกใช้แล้ว โปรดใช้อีเมลอื่น';
  header('Location: register.html'); exit();
}
$chk->close();

$hash = password_hash($password, PASSWORD_DEFAULT);

$ins = $db->prepare('INSERT INTO `user` (user_name, user_email, user_tel, user_password) VALUES (?,?,?,?)');
if (!$ins) { $_SESSION['error'] = 'DB error: prepare(insert)'; header('Location: register.html'); exit(); }

$ins->bind_param('ssss', $name, $email, $phone, $hash);

try {
  $ins->execute();
  $ins->close();
  $_SESSION['success'] = 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ';
  header('Location: Login.php'); exit();
} catch (mysqli_sql_exception $e) {
  $ins->close();
  $_SESSION['error'] = 'DB error: '.$e->getMessage();
  header('Location: register.html'); exit();
}
