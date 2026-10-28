<?php
// handle/auth_process.php
session_start();
require_once __DIR__ . '/../database/db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        header("Location: ../views/login.php?error=empty_fields");
        exit();
    }

    try {
        // 1. Kiểm tra trước trong bảng admins
        $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
        $stmt->execute([$username]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && ($password === $admin['password'] || password_verify($password, $admin['password']))) {
            $_SESSION['user'] = $admin['username'];
            $_SESSION['role'] = 'admin';
            header("Location: ../views/dashboard.php");
            exit();
        }

        // 2. Nếu không phải admin, kiểm tra tiếp trong bảng users
        $stmtUser = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $stmtUser->execute([$username]);
        $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

        if ($user && ($password === $user['password'] || password_verify($password, $user['password']))) {
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            header("Location: ../views/dashboard.php");
            exit();
        }

        // Nếu sai tài khoản hoặc mật khẩu
        header("Location: ../views/login.php?error=invalid_credentials");
        exit();

    } catch (PDOException $e) {
        die("Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage());
    }
} else {
    header("Location: ../views/login.php");
    exit();
}