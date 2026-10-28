<?php
// views/login.php
session_start();

// 1. Nếu người dùng đã đăng nhập từ trước, tự động chuyển vào Dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Nhom15 Analytics</title>
    <!-- Nhúng file CSS giao diện hiện đại -->
    <link rel="stylesheet" href="../css/login.css">
</head>

<body class="login-page">
    <div class="login-container">
        <!-- Logo & Tiêu đề -->
        <div style="text-align: center;">
            <div class="brand-badge">
                <span>📊</span> Nhom15 Analytics
            </div>
            <h2>Đăng Nhập</h2>
            <p class="login-subtitle">Hệ thống thống kê truy cập & phân tích thao tác website</p>
        </div>

        <!-- 2. Thông báo thành công (sau khi vừa đăng ký tài khoản từ register_process.php) -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'registered'): ?>
            <div class="alert-box success">
                <span style="font-size: 1.1rem;">✔</span>
                <span>Đăng ký thành công! Bạn có thể đăng nhập ngay bằng tài khoản vừa tạo.</span>
            </div>
        <?php endif; ?>

        <!-- 3. Thông báo lỗi chuẩn xác theo mã lỗi từ auth_process.php -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert-box error">
                <span style="font-size: 1.1rem;">⚠️</span>
                <span>
                    <?php
                    $err = $_GET['error'];
                    if ($err === 'empty_fields') {
                        echo 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu!';
                    } elseif ($err === 'invalid_credentials') {
                        echo 'Tên đăng nhập hoặc mật khẩu không chính xác!';
                    } else {
                        echo htmlspecialchars($err);
                    }
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <!-- 4. Form gửi dữ liệu sang backend handle/auth_process.php -->
        <form action="../handle/auth_process.php" method="POST">
            <div class="input-group">
                <label for="username">Tên đăng nhập</label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" id="username" name="username" placeholder="Nhập admin hoặc user1..." required
                        autocomplete="username">
                </div>
            </div>

            <div class="input-group">
                <label for="password">Mật khẩu</label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="••••••••" required
                        autocomplete="current-password">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>Đăng Nhập Hệ Thống</span>
                <span style="font-size: 1.1rem;">➔</span>
            </button>
        </form>

        <!-- 5. Phím tắt điền nhanh tài khoản kiểm thử (Rất tiện khi báo cáo cho thầy cô) -->
        <div class="demo-accounts">
            <div class="demo-accounts-title">
                <span>⚡</span> Bấm chọn nhanh tài khoản thử nghiệm:
            </div>
            <div class="demo-account-pills">
                <div class="demo-pill" onclick="quickFill('admin', '123456')">
                    <strong>Admin:</strong> admin / 123456
                </div>
                <div class="demo-pill" onclick="quickFill('user1', '123456')">
                    <strong>User:</strong> user1 / 123456
                </div>
            </div>
        </div>

        <!-- 6. Chuyển hướng sang trang đăng ký -->
        <p class="auth-link" style="text-align: center;">
            Chưa có tài khoản? <a href="register.php">Đăng ký tài khoản mới</a>
        </p>
    </div>

    <!-- Script tự động điền khi bấm vào thẻ tài khoản mẫu -->
    <script>
        function quickFill(user, pass) {
            document.getElementById('username').value = user;
            document.getElementById('password').value = pass;
        }
    </script>
</body>

</html>