<?php
// views/components/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Analytics - Nhom15</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/admin.css">
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">Web Analytics</div>
        <ul class="sidebar-menu">
            <li><a href="../dashboard.php">Thống kê & Báo cáo</a></li>
            <li><a href="../funnels.php">Phân tích Chuỗi (Funnel)</a></li>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <li><a href="../websites.php">Quản lý Website</a></li>
            <?php endif; ?>
            <li><a href="../test_sites/site1/index.php" target="_blank">Mở Website thử nghiệm</a></li>
            <li>
                <a href="../../handle/logout.php" style="color: #fca5a5;">
                    Đăng xuất (<?= htmlspecialchars($_SESSION['user'] ?? 'Khách') ?>)
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <div class="top-navbar">
            <h2>Hệ thống Thống kê & Phân tích Thao tác Website</h2>
            <div>
                Xin chào, <b><?= htmlspecialchars($_SESSION['user'] ?? 'Khách') ?></b>
                (Quyền: <em><?= htmlspecialchars($_SESSION['role'] ?? 'user') ?></em>)
            </div>
        </div>
        <div class="container-fluid" style="padding: 20px;">