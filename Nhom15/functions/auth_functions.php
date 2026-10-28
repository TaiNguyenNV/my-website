<?php
// functions/auth_functions.php

// Kiểm tra quyền xem báo cáo của người dùng trên website
function checkWebsitePermission($pdo, $website_id, $username)
{
    $stmt = $pdo->prepare("SELECT allowed_users FROM websites WHERE id = ?");
    $stmt->execute([$website_id]);
    $website = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$website) {
        return false;
    }

    $allowed_users = explode(',', str_replace(' ', '', $website['allowed_users']));

    // Kiểm tra xem username có nằm trong danh sách được phép hay không
    return in_array($username, $allowed_users);
}

// Kiểm tra trạng thái đăng nhập chung
function requireLogin()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user'])) {
        header("Location: ../views/login.php");
        exit();
    }
}

// Kiểm tra quyền Quản trị viên (Admin) - Chặn user thông thường
function requireAdmin()
{
    requireLogin();
    if (($_SESSION['role'] ?? '') !== 'admin') {
        die("<div style='padding: 50px 20px; text-align: center; font-family: sans-serif;'>
            <h2 style='color: #ef4444; font-size: 24px; margin-bottom: 12px;'>⛔ Truy cập bị từ chối</h2>
            <p style='color: #4b5563; font-size: 16px;'>Chỉ tài khoản có quyền <b>Quản trị viên (Admin)</b> mới được phép truy cập và quản lý website.</p>
            <a href='dashboard.php' style='display: inline-block; margin-top: 20px; padding: 10px 22px; background: #4f46e5; color: white; text-decoration: none; border-radius: 8px; font-weight: 600;'>Quay lại Dashboard</a>
        </div>");
    }
}
?>