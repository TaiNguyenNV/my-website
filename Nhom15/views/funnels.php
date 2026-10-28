<?php
// views/funnels.php
session_start();
require_once '../database/db_connection.php';
require_once '../functions/auth_functions.php';
require_once '../functions/funnel_functions.php';

requireLogin();

$website_id = intval($_GET['website_id'] ?? 1);
$websites = $pdo->query("SELECT * FROM websites")->fetchAll(PDO::FETCH_ASSOC);
$funnel = getFunnelAnalysis($pdo, $website_id);
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Phân tích Chuỗi Thao tác - Đề tài 17</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-brand">📊 Web Analytics</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Thống kê & Báo cáo</a></li>
            <li class="active"><a href="funnels.php">Phân tích Chuỗi (Funnel)</a></li>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <li><a href="websites.php">Quản lý Website</a></li>
            <?php endif; ?>
            <li><a href="test_sites/site1/index.php" target="_blank">Mở Website thử nghiệm</a></li>
            <li><a href="../handle/logout.php" style="color: #fca5a5;">Đăng xuất</a></li>
        </ul>
    </div>
    <div class="main-content">
        <div class="top-navbar">
            <h2>Phân tích Chuỗi thao tác (Funnel Sequence)</h2>
        </div>
        <div class="container-fluid">
            <form method="GET" class="card" style="padding: 16px; margin-bottom: 24px;">
                <label><b>Chọn Website:</b></label>
                <select name="website_id" onchange="this.form.submit()" class="form-control"
                    style="max-width: 300px; display: inline-block; margin-left: 10px;">
                    <?php foreach ($websites as $ws): ?>
                        <option value="<?= $ws['id'] ?>" <?= $ws['id'] == $website_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ws['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>

            <?php
            $pct_start = ($funnel['step_view'] > 0) ? round(($funnel['step_start'] / $funnel['step_view']) * 100, 1) : 0;
            $pct_submit = ($funnel['step_start'] > 0) ? round(($funnel['step_submit'] / $funnel['step_start']) * 100, 1) : 0;
            $pct_total = ($funnel['step_view'] > 0) ? round(($funnel['step_submit'] / $funnel['step_view']) * 100, 1) : 0;
            ?>
            <div class="card-table">
                <div class="card-header" style="display: flex; justify-content: space-between;">
                    <span>Tỷ lệ chuyển đổi qua từng bước (Xem trang ➔ Bắt đầu đăng ký ➔ Hoàn tất)</span>
                    <span style="color: #4f46e5; font-weight: bold;">Tổng tỷ lệ chuyển đổi: <?= $pct_total ?>%</span>
                </div>
                <div class="funnel-steps">
                    <div class="funnel-step-item">
                        <h4>Tổng số phiên (Sessions)</h4>
                        <div class="value" style="color: var(--text-main);">
                            <?= $funnel['total_sessions'] ?>
                        </div>
                    </div>
                    <div class="funnel-step-item">
                        <h4>Bước 1: Xem trang (Pageview)</h4>
                        <div class="value" style="color: #0284c7;">
                            <?= $funnel['step_view'] ?>
                        </div>
                        <p style="font-size: 0.78rem; color: #64748b; margin-top: 4px;">Mốc bắt đầu</p>
                    </div>
                    <div class="funnel-step-item">
                        <h4>Bước 2: Bấm Đăng ký (Start)</h4>
                        <div class="value" style="color: #d97706;">
                            <?= $funnel['step_start'] ?>
                        </div>
                        <p style="font-size: 0.78rem; color: #d97706; margin-top: 4px;">Tỷ lệ: <?= $pct_start ?>%</p>
                    </div>
                    <div class="funnel-step-item">
                        <h4>Bước 3: Hoàn tất (Submit)</h4>
                        <div class="value" style="color: #16a34a;">
                            <?= $funnel['step_submit'] ?>
                        </div>
                        <p style="font-size: 0.78rem; color: #16a34a; margin-top: 4px;">Tỷ lệ: <?= $pct_submit ?>%</p>
                    </div>
                </div>
            </div>
        </div>
        <footer class="main-footer">Nhóm15 &copy; 2026</footer>
    </div>
</body>

</html>