<?php
// views/dashboard.php
session_start();
require_once '../database/db_connection.php';
require_once '../functions/auth_functions.php';
require_once '../functions/report_functions.php';
require_once '../functions/funnel_functions.php';

// Kiểm tra đăng nhập
requireLogin();

$username = $_SESSION['user'];
$website_id = intval($_GET['website_id'] ?? 1);

// Kiểm tra quyền xem báo cáo của user trên website được chọn
if (!checkWebsitePermission($pdo, $website_id, $username)) {
    die("<div style='padding: 40px; text-align: center; font-family: sans-serif;'><h2>Truy cập bị từ chối</h2><p>Tài khoản <b>" . htmlspecialchars($username) . "</b> không có quyền xem báo cáo của website ID $website_id.</p><a href='login.php'>Quay lại đăng nhập</a></div>");
}

// Lấy danh sách website để hiển thị vào bộ lọc chọn
$websites = $pdo->query("SELECT * FROM websites")->fetchAll(PDO::FETCH_ASSOC);

// Lọc ngày tháng (Mặc định 30 ngày gần nhất theo múi giờ UTC+7)
$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Lấy dữ liệu thống kê từ database
$summary = getWebsiteSummary($pdo, $website_id, $date_from, $date_to . ' 23:59:59');
$overall = getOverallStats($pdo, $website_id, $date_from, $date_to . ' 23:59:59');
$funnel = getFunnelAnalysis($pdo, $website_id);
$topActions = getTopClickedActions($pdo, $website_id, $date_from, $date_to . ' 23:59:59', 8);

// Tính tỷ lệ chuyển đổi Funnel
$conversion_rate = ($funnel['step_view'] > 0) ? round(($funnel['step_submit'] / $funnel['step_view']) * 100, 1) : 0;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Thống kê - Nhom15 Analytics</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .kpi-card {
            background: #ffffff;
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 10px;
            padding: 18px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .kpi-card .kpi-label {
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .kpi-card .kpi-number {
            font-size: 1.8rem;
            font-weight: 700;
            margin-top: 8px;
            color: #1f2937;
        }

        .badge-event {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.78rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-search {
            background: #e0f2fe;
            color: #0369a1;
        }

        .badge-start {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-download {
            background: #f3e8ff;
            color: #7e22ce;
        }

        .badge-submit {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-click {
            background: #f1f5f9;
            color: #475569;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">Nhom15 Analytics</div>
        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard.php">Thống kê & Báo cáo</a></li>
            <li><a href="funnels.php">Phân tích Chuỗi (Funnel)</a></li>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <li><a href="websites.php">Quản lý Website</a></li>
            <?php endif; ?>
            <!-- Đã bổ sung liên kết đến cả 2 website thử nghiệm -->
            <li><a href="test_sites/site1/index.php" target="_blank">🛍️ Mở Website thử nghiệm 1</a></li>
            <li><a href="test_sites/site2/index.php" target="_blank">☁️ Mở Website thử nghiệm 2</a></li>
            <li>
                <a href="../handle/logout.php" style="color: #fca5a5;">
                    Đăng xuất (<?= htmlspecialchars($username) ?>)
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="top-navbar">
            <h2>Bảng điều khiển phân tích số liệu</h2>
            <div>Xin chào, <b><?= htmlspecialchars($username) ?></b> (Quyền:
                <em><?= htmlspecialchars($_SESSION['role'] ?? 'user') ?></em>)
            </div>
        </div>

        <div class="container-fluid">
            <!-- Bộ lọc Website & Thời gian -->
            <form method="GET" class="admin-header-actions card"
                style="padding: 16px; background: white; border-radius: 8px; display: flex; flex-wrap: wrap; gap: 15px; align-items: center;">
                <div class="filter-group" style="display: flex; align-items: center; gap: 10px;">
                    <label><b>Chọn Website:</b></label>
                    <select name="website_id" onchange="this.form.submit()" class="form-control" style="margin:0;">
                        <?php foreach ($websites as $ws): ?>
                            <option value="<?= $ws['id'] ?>" <?= $ws['id'] == $website_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ws['name']) ?> (<?= htmlspecialchars($ws['domain']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group" style="display: flex; align-items: center; gap: 10px;">
                    <label>Từ ngày:</label>
                    <input type="date" name="date_from" value="<?= $date_from ?>" class="form-control"
                        style="margin:0;">
                    <label>Đến:</label>
                    <input type="date" name="date_to" value="<?= $date_to ?>" class="form-control" style="margin:0;">
                    <button type="submit" class="btn btn-primary">Lọc dữ liệu</button>
                    <a href="../handle/export_report.php?website_id=<?= $website_id ?>&date_from=<?= $date_from ?>&date_to=<?= $date_to ?>"
                        class="btn"
                        style="background: var(--success-color, #28a745); color: white; text-decoration: none; padding: 8px 14px; border-radius: 6px;">📥
                        Xuất CSV</a>
                </div>
            </form>

            <!-- 1. Thẻ chỉ số tổng quan (KPI Overview) -->
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-label">👁️ Lượt xem trang</div>
                    <div class="kpi-number" style="color: #4f46e5;"><?= number_format($overall['total_pageviews']) ?>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">👥 Số phiên hoạt động</div>
                    <div class="kpi-number" style="color: #0284c7;"><?= number_format($overall['active_sessions']) ?>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">🔍 Lượt tìm kiếm</div>
                    <div class="kpi-number" style="color: #0369a1;"><?= number_format($overall['total_searches']) ?>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">📝 Bấm nút đăng ký</div>
                    <div class="kpi-number" style="color: #d97706;"><?= number_format($overall['total_starts']) ?></div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">📥 Tải tài liệu</div>
                    <div class="kpi-number" style="color: #7e22ce;"><?= number_format($overall['total_downloads']) ?>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">✅ Hoàn tất biểu mẫu</div>
                    <div class="kpi-number" style="color: #16a34a;"><?= number_format($overall['total_submits']) ?>
                    </div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-label">⚡ Tổng thao tác click</div>
                    <div class="kpi-number" style="color: #ea580c;"><?= number_format($overall['total_actions']) ?>
                    </div>
                </div>
            </div>

            <!-- 2. Phân tích Chuỗi Thao tác (Funnel Analysis) -->
            <div class="analytics-table-card" style="margin-top: 10px; margin-bottom: 24px;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>Phân tích chuỗi thao tác (Funnel: Xem trang ➔ Bắt đầu đăng ký ➔ Hoàn tất)</span>
                    <span style="font-size: 0.9rem; font-weight: 500; color: #4f46e5;">
                        Tỷ lệ chuyển đổi chung: <b><?= $conversion_rate ?>%</b>
                    </span>
                </div>
                <div class="funnel-steps" style="display: flex; gap: 20px; padding: 20px;">
                    <div class="funnel-step-item"
                        style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 6px; text-align: center;">
                        <h4>1. Lượt xem (Pageview)</h4>
                        <div class="value"
                            style="font-size: 24px; font-weight: bold; color: #007bff; margin-top: 10px;">
                            <?= $funnel['step_view'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="funnel-step-item"
                        style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 6px; text-align: center;">
                        <h4>2. Bắt đầu đăng ký (Start)</h4>
                        <div class="value"
                            style="font-size: 24px; font-weight: bold; color: #ffc107; margin-top: 10px;">
                            <?= $funnel['step_start'] ?? 0 ?>
                        </div>
                    </div>
                    <div class="funnel-step-item"
                        style="flex: 1; background: #f8f9fa; padding: 15px; border-radius: 6px; text-align: center;">
                        <h4>3. Hoàn tất gửi form (Submit)</h4>
                        <div class="value"
                            style="font-size: 24px; font-weight: bold; color: #28a745; margin-top: 10px;">
                            <?= $funnel['step_submit'] ?? 0 ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Bảng Thống kê Chi tiết Từng Loại Sự Kiện Theo Ngày -->
            <div class="card-table">
                <div class="card-header">Báo cáo tổng hợp chi tiết theo ngày (Múi giờ chuẩn UTC+7)</div>
                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; text-align: left; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px 14px;">Ngày báo cáo</th>
                                <th style="padding: 12px 14px;">Lượt xem (Views)</th>
                                <th style="padding: 12px 14px;">Số phiên</th>
                                <th style="padding: 12px 14px;">🔍 Tìm kiếm</th>
                                <th style="padding: 12px 14px;">📝 Bấm đăng ký</th>
                                <th style="padding: 12px 14px;">📥 Tải tài liệu</th>
                                <th style="padding: 12px 14px;">✅ Hoàn tất</th>
                                <th style="padding: 12px 14px;">Tổng thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($summary)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 25px; color: #6c757d;">
                                        Chưa có dữ liệu sự kiện nào trong khoảng thời gian này.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($summary as $row): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px 14px;"><b><?= htmlspecialchars($row['report_date']) ?></b></td>
                                        <td style="padding: 12px 14px; color: #4f46e5; font-weight: 600;">
                                            <?= $row['total_pageviews'] ?>
                                        </td>
                                        <td style="padding: 12px 14px;"><?= $row['active_sessions'] ?></td>
                                        <td style="padding: 12px 14px;">
                                            <span class="badge-event badge-search"><?= $row['total_searches'] ?></span>
                                        </td>
                                        <td style="padding: 12px 14px;">
                                            <span class="badge-event badge-start"><?= $row['total_starts'] ?></span>
                                        </td>
                                        <td style="padding: 12px 14px;">
                                            <span class="badge-event badge-download"><?= $row['total_downloads'] ?></span>
                                        </td>
                                        <td style="padding: 12px 14px;">
                                            <span class="badge-event badge-submit"><?= $row['total_submits'] ?></span>
                                        </td>
                                        <td style="padding: 12px 14px; font-weight: bold; color: #ea580c;">
                                            <?= $row['total_actions'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <?php if (!empty($summary)): ?>
                            <tfoot>
                                <tr style="background: #f1f5f9; font-weight: bold; border-top: 2px solid #cbd5e1;">
                                    <td style="padding: 12px 14px;">TỔNG CỘNG</td>
                                    <td style="padding: 12px 14px; color: #4f46e5;">
                                        <?= number_format($overall['total_pageviews']) ?>
                                    </td>
                                    <td style="padding: 12px 14px;"><?= number_format($overall['active_sessions']) ?></td>
                                    <td style="padding: 12px 14px; color: #0369a1;">
                                        <?= number_format($overall['total_searches']) ?>
                                    </td>
                                    <td style="padding: 12px 14px; color: #b45309;">
                                        <?= number_format($overall['total_starts']) ?>
                                    </td>
                                    <td style="padding: 12px 14px; color: #7e22ce;">
                                        <?= number_format($overall['total_downloads']) ?>
                                    </td>
                                    <td style="padding: 12px 14px; color: #15803d;">
                                        <?= number_format($overall['total_submits']) ?>
                                    </td>
                                    <td style="padding: 12px 14px; color: #ea580c;">
                                        <?= number_format($overall['total_actions']) ?>
                                    </td>
                                </tr>
                            </tfoot>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <!-- 4. Bảng Phân tích Các Nút Bấm / Thao Tác Được Nhấp Nhiều Nhất -->
            <div class="card-table" style="margin-top: 24px;">
                <div class="card-header">
                    <span>Phân tích các nút bấm & thao tác được người dùng nhấp nhiều nhất (Top Clicked Actions)</span>
                </div>
                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; text-align: left; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px 14px;">Nút bấm / Thao tác được click</th>
                                <th style="padding: 12px 14px;">Loại sự kiện (Event Type)</th>
                                <th style="padding: 12px 14px;">Đường dẫn trang (URL)</th>
                                <th style="padding: 12px 14px; text-align: right;">Số lần nhấp (Clicks)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topActions)): ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 20px; color: #6c757d;">
                                        Chưa có thao tác nhấp chuột nào được ghi nhận.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($topActions as $act): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px 14px;">
                                            <b><?= htmlspecialchars($act['action_name']) ?></b>
                                        </td>
                                        <td style="padding: 12px 14px;">
                                            <?php
                                            $evClass = 'badge-click';
                                            if ($act['event_type'] === 'search')
                                                $evClass = 'badge-search';
                                            elseif ($act['event_type'] === 'start')
                                                $evClass = 'badge-start';
                                            elseif ($act['event_type'] === 'download')
                                                $evClass = 'badge-download';
                                            elseif ($act['event_type'] === 'submit')
                                                $evClass = 'badge-submit';
                                            ?>
                                            <span class="badge-event <?= $evClass ?>">
                                                <?= htmlspecialchars($act['event_type']) ?>
                                            </span>
                                        </td>
                                        <td style="padding: 12px 14px; color: #64748b; font-size: 0.9rem;">
                                            <?= htmlspecialchars($act['page_url']) ?>
                                        </td>
                                        <td style="padding: 12px 14px; text-align: right; font-weight: bold; color: #4f46e5;">
                                            <?= number_format($act['click_count']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <footer class="main-footer" style="text-align: center; padding: 20px; color: #6c757d; margin-top: 30px;">
            Công cụ thống kê lượt truy cập và thao tác trên website Nhóm15 &copy; 2026
        </footer>
    </div>
</body>

</html>