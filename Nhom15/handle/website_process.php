<?php
// handle/website_process.php
session_start();
require_once '../database/db_connection.php';
require_once '../functions/auth_functions.php';

// Chỉ cho phép Quản trị viên (Admin) thêm website
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $domain = trim($_POST['domain'] ?? '');
    $allowed_users = trim($_POST['allowed_users'] ?? 'admin');

    if (!empty($name) && !empty($domain)) {
        $stmt = $pdo->prepare("INSERT INTO websites (name, domain, allowed_users) VALUES (?, ?, ?)");
        $stmt->execute([$name, $domain, $allowed_users]);

        header("Location: ../views/websites.php?success=1");
        exit();
    } else {
        header("Location: ../views/websites.php?error=Thiếu thông tin tên hoặc domain");
        exit();
    }
} else {
    header("Location: ../views/websites.php");
    exit();
}
?>