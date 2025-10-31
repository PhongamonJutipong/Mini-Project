<?php
session_start();
require 'conn.php';
$db = $mysqli; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { 
    echo "<script>
        alert('Invalid access to this page.');
        window.location.href = 'Register.php';
    </script>";
    exit(); 
}

$name     = trim($_POST['user_name']      ?? $_POST['username'] ?? '');
$email    = trim($_POST['user_email']     ?? $_POST['email']    ?? '');
$phone    = trim($_POST['user_tel']       ?? $_POST['phone']    ?? '');
$password =        $_POST['user_password']?? $_POST['password'] ?? '';
$confirm  =        $_POST['confirm_password'] ?? '';

// Check if fields are empty
if ($name === '' || $email === '' || $phone === '' || $password === '') {
    echo "<script>
        alert('Please fill in all required fields.');
        window.history.back();
    </script>";
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "<script>
        alert('Invalid email format.');
        window.history.back();
    </script>";
    exit();
}

// Check if passwords match
if ($confirm !== '' && $password !== $confirm) {
    echo "<script>
        alert('Passwords do not match.');
        window.history.back();
    </script>";
    exit();
}

// Check if email already exists
$chk = $db->prepare('SELECT 1 FROM `user` WHERE user_email = ? LIMIT 1');
if (!$chk) { 
    echo "<script>
        alert('System error occurred. Please try again.');
        window.history.back();
    </script>";
    exit(); 
}

$chk->bind_param('s', $email);
$chk->execute();
$chk->store_result();

if ($chk->num_rows > 0) {
    $chk->close();
    echo "<script>
        alert('This email is already registered. Please use a different email.');
        window.history.back();
    </script>";
    exit();
}
$chk->close();

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Prepare SQL statement for insert
$ins = $db->prepare('INSERT INTO `user` (user_name, user_email, user_tel, user_password) VALUES (?,?,?,?)');
if (!$ins) { 
    echo "<script>
        alert('System error occurred. Please try again.');
        window.history.back();
    </script>";
    exit(); 
}

$ins->bind_param('ssss', $name, $email, $phone, $hash);

try {
    $ins->execute();
    $ins->close();

    echo "<script>
        alert('Registration successful! Please login.');
        window.location.href = 'Login.php';
    </script>";
    exit(); 

} catch (mysqli_sql_exception $e) {
    $ins->close();
    echo "<script>
        alert('Failed to save data. Please try again.');
        window.history.back();
    </script>";
    exit();
}
?>