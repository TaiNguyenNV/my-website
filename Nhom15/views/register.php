<?php
// views/register.php
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
    <title>Đăng ký tài khoản - Nhom15 Analytics</title>
    <!-- Nhúng file CSS giao diện hiện đại -->
    <link rel="stylesheet" href="../css/login.css">
</head>

<body class="login-page">
    <div class="login-container">
        <!-- Logo & Tiêu đề -->
        <div style="text-align: center;">
            <div class="brand-badge">
                <span>✨</span> Nhom15 Analytics
            </div>
            <h2>Tạo Tài Khoản</h2>
            <p class="login-subtitle">Đăng ký tài khoản người dùng để xem báo cáo phân tích</p>
        </div>

        <!-- 2. Thông báo lỗi chuẩn xác theo mã lỗi từ handle/register_process.php -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert-box error">
                <span style="font-size: 1.1rem;">⚠️</span>
                <span>
                    <?php
                    $err = $_GET['error'];
                    if ($err === 'empty_fields') {
                        echo 'Vui lòng điền đầy đủ các trường thông tin bắt buộc!';
                    } elseif ($err === 'password_mismatch') {
                        echo 'Mật khẩu xác nhận không khớp!';
                    } elseif ($err === 'username_exists') {
                        echo 'Tên đăng nhập này đã tồn tại trên hệ thống, vui lòng chọn tên khác!';
                    } else {
                        echo htmlspecialchars($err);
                    }
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <!-- 3. Form gửi dữ liệu sang backend handle/register_process.php -->
        <form action="../handle/register_process.php" method="POST" id="registerForm">
            <div class="input-group">
                <label for="username">Tên đăng nhập <span style="color: var(--error-text);">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon">👤</span>
                    <input type="text" id="username" name="username" placeholder="Ví dụ: nguyenvana..." required
                        autocomplete="username">
                </div>
            </div>

            <div class="input-group">
                <label for="email">Địa chỉ Email</label>
                <div class="input-wrapper">
                    <span class="input-icon">✉️</span>
                    <input type="email" id="email" name="email" placeholder="email@domain.com" autocomplete="email">
                </div>
            </div>

            <div class="input-group">
                <label for="password">Mật khẩu <span style="color: var(--error-text);">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon">🔒</span>
                    <input type="password" id="password" name="password" placeholder="Tối thiểu 6 ký tự..." required
                        autocomplete="new-password">
                </div>
            </div>

            <div class="input-group">
                <label for="confirm_password">Xác nhận mật khẩu <span style="color: var(--error-text);">*</span></label>
                <div class="input-wrapper">
                    <span class="input-icon">🛡️</span>
                    <input type="password" id="confirm_password" name="confirm_password"
                        placeholder="Nhập lại mật khẩu..." required autocomplete="new-password">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <span>Hoàn Tất Đăng Ký</span>
                <span style="font-size: 1.1rem;">➔</span>
            </button>
        </form>

        <!-- 4. Chuyển hướng về trang đăng nhập -->
        <p class="auth-link" style="text-align: center;">
            Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
        </p>
    </div>

    <!-- Script kiểm tra khớp mật khẩu ngay trên trình duyệt -->
    <script>
        const form = document.getElementById('registerForm');
        const pass = document.getElementById('password');
        const confirmPass = document.getElementById('confirm_password');

        form.addEventListener('submit', function (e) {
            if (pass.value !== confirmPass.value) {
                e.preventDefault();
                alert('Mật khẩu xác nhận không khớp! Vui lòng kiểm tra lại.');
                confirmPass.focus();
            }
        });
    </script>
</body>

</html>