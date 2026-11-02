<?php
// views/dashboard.php
session_start();
require_once '../database/db_connection.php';
require_once '../functions/auth_functions.php';
require_once '../functions/report_functions.php';
require_once '../functions/funnel_functions.php';

// Kiểm tra đăng nhập
requireLogin();

date_default_timezone_set('Asia/Ho_Chi_Minh');

$username = $_SESSION['user'];

// Đường dẫn tới thư mục test_sites (đổi thành 'test_sites' nếu thư mục nằm trong views/)
$testBase = '../test_sites';

// Lấy danh sách website để hiển thị vào bộ lọc chọn
$websites = $pdo->query("SELECT * FROM websites ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Nếu không truyền website_id thì lấy website đầu tiên trong DB (thay vì mặc định cứng là 1)
$website_id = intval($_GET['website_id'] ?? ($websites[0]['id'] ?? 1));

// Kiểm tra quyền xem báo cáo của user trên website được chọn
if (!checkWebsitePermission($pdo, $website_id, $username)) {
    die("<div style='padding: 40px; text-align: center; font-family: sans-serif;'><h2>Truy cập bị từ chối</h2><p>Tài khoản <b>" . htmlspecialchars($username) . "</b> không có quyền xem báo cáo của website ID $website_id.</p><a href='login.php'>Quay lại đăng nhập</a></div>");
}

// Lọc ngày tháng (mặc định 30 ngày gần nhất, múi giờ UTC+7) + kiểm tra định dạng
$isDate = function ($d) {
    return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $d);
};
$date_from = $isDate($_GET['date_from'] ?? '') ? $_GET['date_from'] : date('Y-m-d', strtotime('-30 days'));
$date_to = $isDate($_GET['date_to'] ?? '') ? $_GET['date_to'] : date('Y-m-d');

// Lấy dữ liệu thống kê từ database
$summary = getWebsiteSummary($pdo, $website_id, $date_from, $date_to . ' 23:59:59');
$overall = getOverallStats($pdo, $website_id, $date_from, $date_to . ' 23:59:59');
$funnel = getFunnelAnalysis($pdo, $website_id);
$topActions = getTopClickedActions($pdo, $website_id, $date_from, $date_to . ' 23:59:59', 8);

// Funnel steps
$step_view = $funnel['step_view'] ?? 0;
$step_start = $funnel['step_start'] ?? 0;
$step_submit = $funnel['step_submit'] ?? 0;

// Tỷ lệ chuyển đổi Funnel
$conversion_rate = ($step_view > 0) ? round(($step_submit / $step_view) * 100, 1) : 0;
$rate_start = ($step_view > 0) ? round($step_start / $step_view * 100, 1) : 0;
$rate_submit = ($step_start > 0) ? round($step_submit / $step_start * 100, 1) : 0;

// Dữ liệu Chart.js
$chart_labels = [];
$chart_views = [];
$chart_actions = [];
foreach ($summary as $row) {
    $chart_labels[] = $row['report_date'];
    $chart_views[] = (int) $row['total_pageviews'];
    $chart_actions[] = (int) $row['total_actions'];
}
$chart_labels_json = json_encode($chart_labels);
$chart_views_json = json_encode($chart_views);
$chart_actions_json = json_encode($chart_actions);

// Max click count cho progress bar
$max_clicks = 0;
foreach ($topActions as $act) {
    if ((int) $act['click_count'] > $max_clicks)
        $max_clicks = (int) $act['click_count'];
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Thống kê</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">Nhom15</div>
        <ul class="sidebar-menu">
            <li class="active"><a href="dashboard.php">Thống kê & Báo cáo</a></li>
            <li><a href="funnels.php">Phân tích Chuỗi (Funnel)</a></li>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <li><a href="websites.php">Quản lý Website</a></li>
            <?php endif; ?>
            <li><a href="<?= $testBase ?>/site1/index.php" target="_blank">🛍️ Store</a></li>
            <li><a href="<?= $testBase ?>/site2/index.php" target="_blank">☁️ Cloud Services</a></li>
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
            <form method="GET" id="filterForm" class="admin-header-actions card"
                style="padding: 16px; background: white; border-radius: 10px; display: flex; flex-wrap: wrap; gap: 14px; align-items: center; margin-bottom: 20px;">
                <div class="filter-group" style="display: flex; align-items: center; gap: 10px;">
                    <label><b>Chọn Website:</b></label>
                    <select name="website_id" onchange="this.form.submit()" class="form-control" style="margin:0;">
                        <?php foreach ($websites as $ws): ?>
                            <option value="<?= (int) $ws['id'] ?>" <?= $ws['id'] == $website_id ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ws['name']) ?> (<?= htmlspecialchars($ws['domain']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                    <!-- Quick presets -->
                    <div class="date-presets">
                        <span style="font-size:0.8rem; color:#6b7280; font-weight:600;">Nhanh:</span>
                        <button type="button" class="date-preset-btn" onclick="setDateRange(0)">Hôm nay</button>
                        <button type="button" class="date-preset-btn" onclick="setDateRange(6)">7 ngày</button>
                        <button type="button" class="date-preset-btn" onclick="setDateRange(29)">30 ngày</button>
                        <button type="button" class="date-preset-btn" onclick="setThisMonth()">Tháng này</button>
                    </div>
                    <label>Từ ngày:</label>
                    <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($date_from) ?>"
                        class="form-control" style="margin:0;">
                    <label>Đến:</label>
                    <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($date_to) ?>"
                        class="form-control" style="margin:0;">
                    <button type="submit" class="btn btn-primary">Lọc dữ liệu</button>
                    <a href="../handle/export_report.php?website_id=<?= $website_id ?>&date_from=<?= urlencode($date_from) ?>&date_to=<?= urlencode($date_to) ?>"
                        class="btn"
                        style="background:#16a34a; color:white; text-decoration:none; padding:8px 14px; border-radius:6px;">📥
                        Xuất CSV</a>
                </div>
            </form>

            <!-- 1. KPI Cards với count-up animation -->
            <div class="kpi-grid">
                <div class="kpi-card" style="--kpi-color:#4f46e5;">
                    <div class="kpi-label">👁️ Lượt xem trang</div>
                    <div class="kpi-number count-up" data-target="<?= (int) $overall['total_pageviews'] ?>">0</div>
                </div>
                <div class="kpi-card" style="--kpi-color:#0284c7;">
                    <div class="kpi-label">👥 Số phiên hoạt động</div>
                    <div class="kpi-number count-up" data-target="<?= (int) $overall['active_sessions'] ?>">0</div>
                </div>
                <div class="kpi-card" style="--kpi-color:#0369a1;">
                    <div class="kpi-label">🔍 Lượt tìm kiếm</div>
                    <div class="kpi-number count-up" data-target="<?= (int) $overall['total_searches'] ?>">0</div>
                </div>
                <div class="kpi-card" style="--kpi-color:#d97706;">
                    <div class="kpi-label">📝 Bấm nút đăng ký</div>
                    <div class="kpi-number count-up" data-target="<?= (int) $overall['total_starts'] ?>">0</div>
                </div>
                <div class="kpi-card" style="--kpi-color:#7e22ce;">
                    <div class="kpi-label">📥 Tải tài liệu</div>
                    <div class="kpi-number count-up" data-target="<?= (int) $overall['total_downloads'] ?>">0</div>
                </div>
                <div class="kpi-card" style="--kpi-color:#16a34a;">
                    <div class="kpi-label">✅ Hoàn tất biểu mẫu</div>
                    <div class="kpi-number count-up" data-target="<?= (int) $overall['total_submits'] ?>">0</div>
                </div>
                <div class="kpi-card" style="--kpi-color:#ea580c;">
                    <div class="kpi-label">⚡ Tổng thao tác click</div>
                    <div class="kpi-number count-up" data-target="<?= (int) $overall['total_actions'] ?>">0</div>
                </div>
            </div>

            <!-- 2. Biểu đồ đường Chart.js -->
            <?php if (!empty($summary)): ?>
                <div class="chart-card">
                    <div class="chart-title">📈 Biểu đồ lượt xem & tổng thao tác theo ngày</div>
                    <canvas id="lineChart" height="90"></canvas>
                </div>
            <?php endif; ?>

            <!-- 3. Funnel Analysis -->
            <div class="analytics-table-card" style="margin-bottom: 24px;">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <span>🔀 Phân tích chuỗi thao tác (Funnel: Xem trang ➔ Bắt đầu đăng ký ➔ Hoàn tất)</span>
                    <span
                        style="font-size: 0.88rem; font-weight: 600; color: #4f46e5; background: #eef2ff; padding: 4px 12px; border-radius: 20px;">
                        Tỷ lệ chuyển đổi tổng: <b><?= $conversion_rate ?>%</b>
                    </span>
                </div>
                <div class="funnel-wrapper">
                    <!-- Bước 1 -->
                    <div class="funnel-step">
                        <h4>1. Lượt xem (Pageview)</h4>
                        <div class="funnel-num" style="color:#4f46e5;"><?= number_format($step_view) ?></div>
                        <div class="funnel-bar-wrap">
                            <div class="funnel-bar-fill" style="width:100%; background:#4f46e5;"></div>
                        </div>
                        <div style="font-size:0.75rem; color:#9ca3af; margin-top:6px;">100%</div>
                    </div>
                    <!-- Mũi tên 1→2 -->
                    <div class="funnel-arrow">
                        <div class="arrow-pct"><?= $rate_start ?>%</div>
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    <!-- Bước 2 -->
                    <div class="funnel-step">
                        <h4>2. Bắt đầu đăng ký (Start)</h4>
                        <div class="funnel-num" style="color:#d97706;"><?= number_format($step_start) ?></div>
                        <div class="funnel-bar-wrap">
                            <div class="funnel-bar-fill" style="width:<?= $rate_start ?>%; background:#d97706;"></div>
                        </div>
                        <div style="font-size:0.75rem; color:#9ca3af; margin-top:6px;"><?= $rate_start ?>% từ bước 1
                        </div>
                    </div>
                    <!-- Mũi tên 2→3 -->
                    <div class="funnel-arrow">
                        <div class="arrow-pct"><?= $rate_submit ?>%</div>
                        <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                        </svg>
                    </div>
                    <!-- Bước 3 -->
                    <div class="funnel-step">
                        <h4>3. Hoàn tất gửi form (Submit)</h4>
                        <div class="funnel-num" style="color:#16a34a;"><?= number_format($step_submit) ?></div>
                        <div class="funnel-bar-wrap">
                            <div class="funnel-bar-fill" style="width:<?= $conversion_rate ?>%; background:#16a34a;">
                            </div>
                        </div>
                        <div style="font-size:0.75rem; color:#9ca3af; margin-top:6px;"><?= $conversion_rate ?>% từ bước
                            1</div>
                    </div>
                </div>
            </div>

            <!-- 4. Bảng chi tiết theo ngày -->
            <div class="card-table">
                <div class="card-header">📅 Báo cáo tổng hợp chi tiết theo ngày (Múi giờ chuẩn UTC+7)</div>
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
                                        <td style="padding: 12px 14px;"><span
                                                class="badge-event badge-search"><?= $row['total_searches'] ?></span></td>
                                        <td style="padding: 12px 14px;"><span
                                                class="badge-event badge-start"><?= $row['total_starts'] ?></span></td>
                                        <td style="padding: 12px 14px;"><span
                                                class="badge-event badge-download"><?= $row['total_downloads'] ?></span></td>
                                        <td style="padding: 12px 14px;"><span
                                                class="badge-event badge-submit"><?= $row['total_submits'] ?></span></td>
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

            <!-- 5. Top Clicked Actions với progress bar -->
            <div class="card-table" style="margin-top: 24px;">
                <div class="card-header">
                    🖱️ Phân tích các nút bấm & thao tác được người dùng nhấp nhiều nhất
                </div>
                <div class="table-responsive">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #f8fafc; text-align: left; border-bottom: 2px solid #e2e8f0;">
                                <th style="padding: 12px 14px;">Nút bấm / Thao tác được click</th>
                                <th style="padding: 12px 14px;">Loại sự kiện (Event Type)</th>
                                <th style="padding: 12px 14px;">Đường dẫn trang (URL)</th>
                                <th style="padding: 12px 14px; text-align: right; min-width:140px;">Số lần nhấp (Clicks)
                                </th>
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
                                <?php foreach ($topActions as $act):
                                    $evClass = 'badge-click';
                                    if ($act['event_type'] === 'search')
                                        $evClass = 'badge-search';
                                    elseif ($act['event_type'] === 'start')
                                        $evClass = 'badge-start';
                                    elseif ($act['event_type'] === 'download')
                                        $evClass = 'badge-download';
                                    elseif ($act['event_type'] === 'submit')
                                        $evClass = 'badge-submit';
                                    $pct = $max_clicks > 0 ? round($act['click_count'] / $max_clicks * 100) : 0;
                                    ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9;">
                                        <td style="padding: 12px 14px;"><b><?= htmlspecialchars($act['action_name']) ?></b></td>
                                        <td style="padding: 12px 14px;">
                                            <span
                                                class="badge-event <?= $evClass ?>"><?= htmlspecialchars($act['event_type']) ?></span>
                                        </td>
                                        <td style="padding: 12px 14px; color: #64748b; font-size: 0.9rem;">
                                            <?= htmlspecialchars($act['page_url']) ?>
                                        </td>
                                        <td style="padding: 12px 14px; text-align: right;">
                                            <span
                                                style="font-weight: 800; color: #4f46e5;"><?= number_format($act['click_count']) ?></span>
                                            <div class="action-bar-wrap">
                                                <div class="action-bar-fill" style="width:<?= $pct ?>%;"></div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div><!-- /container-fluid -->

        <footer class="main-footer" style="text-align: center; padding: 20px; color: #6c757d; margin-top: 30px;">
            Công cụ thống kê lượt truy cập và thao tác trên website Nhóm15 &copy; 2026
        </footer>
    </div><!-- /main-content -->

    <script>
        // ── Quick date presets (dùng giờ địa phương, không dùng toISOString để tránh lệch ngày do UTC) ──
        const fmt = d => `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;

        function applyRange(from, to) {
            document.getElementById('date_from').value = fmt(from);
            document.getElementById('date_to').value = fmt(to);
            document.getElementById('filterForm').submit();
        }

        function setDateRange(daysBack) {
            const to = new Date();
            const from = new Date();
            from.setDate(to.getDate() - daysBack);
            applyRange(from, to);
        }

        function setThisMonth() {
            const to = new Date();
            applyRange(new Date(to.getFullYear(), to.getMonth(), 1), to);
        }

        // ── Count-up animation cho KPI cards ──
        function countUp(el) {
            const target = parseInt(el.dataset.target, 10) || 0;
            if (target === 0) { el.textContent = '0'; return; }
            const duration = 900;
            const step = Math.ceil(target / (duration / 16));
            let current = 0;
            const timer = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = current.toLocaleString('vi-VN');
                if (current >= target) clearInterval(timer);
            }, 16);
        }
        document.querySelectorAll('.count-up').forEach(countUp);

        // ── Line Chart ──
        <?php if (!empty($summary)): ?>
            const ctx = document.getElementById('lineChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?= $chart_labels_json ?>,
                    datasets: [
                        {
                            label: 'Lượt xem trang',
                            data: <?= $chart_views_json ?>,
                            borderColor: '#4f46e5',
                            backgroundColor: 'rgba(79,70,229,0.08)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2.5,
                        },
                        {
                            label: 'Tổng thao tác',
                            data: <?= $chart_actions_json ?>,
                            borderColor: '#ea580c',
                            backgroundColor: 'rgba(234,88,12,0.06)',
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            borderWidth: 2.5,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: {
                            callbacks: {
                                label: c => c.dataset.label + ': ' + c.parsed.y.toLocaleString('vi-VN')
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { font: { size: 11 }, callback: v => v.toLocaleString('vi-VN') }
                        }
                    }
                }
            });
        <?php endif; ?>
    </script>
</body>

</html>