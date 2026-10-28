<?php
// handle/register_process.php
session_start();
require_once __DIR__ . '/../database/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Kiểm tra dữ liệu đầu vào cơ bản
    if (empty($username) || empty($password) || empty($confirm_password)) {
        header("Location: ../views/register.php?error=empty_fields");
        exit();
    }

    if ($password !== $confirm_password) {
        header("Location: ../views/register.php?error=password_mismatch");
        exit();
    }

    try {
        // Kiểm tra xem username đã tồn tại trong bảng users chưa
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetch()) {
            header("Location: ../views/register.php?error=username_exists");
            exit();
        }

        // Mã hóa mật khẩu an toàn bằng password_hash
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Thêm tài khoản mới vào bảng users với role mặc định là 'user'
        $insertStmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
        $insertStmt->execute([$username, $hashed_password, $email]);

        // Đăng ký thành công, chuyển hướng về trang login kèm thông báo thành công
        header("Location: ../views/login.php?success=registered");
        exit();

    } catch (PDOException $e) {
        die("Lỗi hệ thống: " . $e->getMessage());
    }
} else {
    header("Location: ../views/register.php");
    exit();
}