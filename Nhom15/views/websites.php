<?php
// views/websites.php
session_start();
require_once '../database/db_connection.php';
require_once '../functions/auth_functions.php';

// Chỉ cho phép Quản trị viên (Admin) truy cập trang này
requireAdmin();

$websites = $pdo->query("SELECT * FROM websites")->fetchAll(PDO::FETCH_ASSOC);
$success = $_GET['success'] ?? '';
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Website - Nhom15 Analytics</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-brand">Nhom15 Analytics</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Thống kê & Báo cáo</a></li>
            <li><a href="funnels.php">Phân tích Chuỗi (Funnel)</a></li>
            <li class="active"><a href="websites.php">Quản lý Website</a></li>
            <li><a href="test_sites/site1/index.php" target="_blank">Mở Website thử nghiệm</a></li>
            <li><a href="../handle/logout.php" style="color: #fca5a5;">Đăng xuất</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="top-navbar">
            <h2>Đăng ký & Quản lý Website Thử nghiệm</h2>
        </div>
        <div class="container-fluid">
            <?php if ($success): ?>
                <div style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px;">
                    Thêm website thử nghiệm thành công!</div>
            <?php endif; ?>

            <div class="card-table" style="padding: 24px; margin-bottom: 24px;">
                <h3>Thêm Website Mới</h3>
                <form action="../handle/website_process.php" method="POST" style="margin-top: 16px;">
                    <label>Tên website:</label>
                    <input type="text" name="name" class="form-control" required placeholder="Ví dụ: Shop Giày Online">

                    <label>Tên miền (Domain):</label>
                    <input type="text" name="domain" class="form-control" required placeholder="Ví dụ: shopgiay.local">

                    <label>Thành viên được phép xem báo cáo (Phân cách bằng dấu phẩy):</label>
                    <input type="text" name="allowed_users" class="form-control" value="admin,manager1" required>

                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">Đăng ký website</button>
                </form>
            </div>

            <div class="card-table">
                <div class="card-header">Danh sách website đang theo dõi</div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Website</th>
                            <th>Domain</th>
                            <th>Thành viên có quyền</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($websites as $ws): ?>
                            <tr>
                                <td><?= $ws['id'] ?></td>
                                <td><b><?= htmlspecialchars($ws['name']) ?></b></td>
                                <td><?= htmlspecialchars($ws['domain']) ?></td>
                                <td><span class="badge badge-success"><?= htmlspecialchars($ws['allowed_users']) ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <footer class="main-footer">Nhóm15 &copy; 2026</footer>
    </div>
</body>

</html>