<?php
// views/funnels.php
session_start();
require_once '../database/db_connection.php';
require_once '../functions/auth_functions.php';
require_once '../functions/funnel_functions.php';

requireLogin();

// Thư mục test_sites nằm trong views/
$testBase = 'test_sites';

function h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

$username = $_SESSION['user'] ?? '';
$websites = $pdo->query("SELECT * FROM websites ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);

// Nếu không truyền website_id thì lấy website đầu tiên trong DB
$website_id = intval($_GET['website_id'] ?? ($websites[0]['id'] ?? 1));

// Kiểm tra quyền xem website được chọn
if (function_exists('checkWebsitePermission') && !checkWebsitePermission($pdo, $website_id, $username)) {
    die("<div style='padding:40px;text-align:center;font-family:sans-serif;'><h2>Truy cập bị từ chối</h2><p>Tài khoản <b>" . h($username) . "</b> không có quyền xem website ID $website_id.</p><a href='dashboard.php'>Quay lại</a></div>");
}

$funnel = getFunnelAnalysis($pdo, $website_id);

$sessions = (int) ($funnel['total_sessions'] ?? 0);
$view = (int) ($funnel['step_view'] ?? 0);
$start = (int) ($funnel['step_start'] ?? 0);
$submit = (int) ($funnel['step_submit'] ?? 0);

$pct = function ($a, $b) {
    return $b > 0 ? round($a / $b * 100, 1) : 0;
};

$pct_start = $pct($start, $view);          // Bước 2 so với bước 1
$pct_submit = $pct($submit, $start);       // Bước 3 so với bước 2
$pct_total = $pct($submit, $view);         // Bước 3 so với bước 1

$drop1 = max($view - $start, 0);
$drop2 = max($start - $submit, 0);
$drop1_pct = $pct($drop1, $view);
$drop2_pct = $pct($drop2, $start);

// Độ rộng thanh (tối thiểu 4% để vẫn nhìn thấy khi số liệu nhỏ)
$w1 = $view > 0 ? 100 : 0;
$w2 = $view > 0 ? max($pct_start, $start > 0 ? 4 : 0) : 0;
$w3 = $view > 0 ? max($pct_total, $submit > 0 ? 4 : 0) : 0;

// Nhận xét tự động
$has_data = $view > 0;
$insight = '';
$insight_tip = '';
if ($has_data) {
    if ($drop1_pct >= $drop2_pct) {
        $insight = "Phần lớn người dùng rời đi ở bước đầu: {$drop1_pct}% lượt xem trang không bấm đăng ký.";
        $insight_tip = 'Thử làm nút đăng ký nổi bật hơn, đặt cao hơn trong trang hoặc rút gọn nội dung dẫn tới nút.';
    } else {
        $insight = "Người dùng bỏ dở nhiều nhất ở bước điền biểu mẫu: {$drop2_pct}% số người bấm đăng ký không gửi form.";
        $insight_tip = 'Thử giảm số trường bắt buộc, báo lỗi rõ ràng hơn hoặc kiểm tra biểu mẫu có lỗi khi gửi không.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phân tích Chuỗi thao tác - Nhom15 Analytics</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap&subset=vietnamese"
        rel="stylesheet">
    <style>
        :root {
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
            --brand: #2563eb;
            --c1: #2563eb;
            --c2: #d97706;
            --c3: #16a34a;
        }

        .page-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .page-head h1 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--ink);
        }

        .page-head p {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .picker {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 8px 12px;
        }

        .picker label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--muted);
        }

        .picker select {
            border: 0;
            background: transparent;
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--ink);
            max-width: 260px;
            cursor: pointer;
        }

        .picker select:focus-visible {
            outline: 2px solid var(--brand);
            outline-offset: 2px;
        }

        .panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            padding: 16px 22px;
            border-bottom: 1px solid var(--line);
        }

        .panel-head h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink);
        }

        .panel-head span {
            font-size: 0.85rem;
            color: var(--muted);
        }

        /* Thẻ tóm tắt */
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        @media (max-width: 800px) {
            .stats {
                grid-template-columns: 1fr;
            }
        }

        .stat {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 18px 20px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .stat .label {
            font-size: 0.85rem;
            color: var(--muted);
            font-weight: 600;
        }

        .stat .num {
            font-size: 2rem;
            font-weight: 800;
            color: var(--ink);
            margin-top: 6px;
            line-height: 1.1;
        }

        .stat .sub {
            font-size: 0.82rem;
            color: var(--muted);
            margin-top: 6px;
        }

        .stat.highlight {
            border-color: #bfdbfe;
            background: #eff6ff;
        }

        .stat.highlight .num {
            color: var(--brand);
        }

        /* Phễu */
        .funnel {
            padding: 26px 22px 10px;
        }

        .f-row {
            display: grid;
            grid-template-columns: 190px 1fr 120px;
            gap: 18px;
            align-items: center;
        }

        @media (max-width: 800px) {
            .f-row {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .f-side {
                text-align: left !important;
            }
        }

        .f-title {
            font-weight: 700;
            color: var(--ink);
            font-size: 0.95rem;
        }

        .f-title small {
            display: block;
            font-weight: 500;
            color: var(--muted);
            font-size: 0.8rem;
            margin-top: 2px;
        }

        .f-track {
            background: #f3f4f6;
            border-radius: 10px;
            height: 52px;
            position: relative;
            overflow: hidden;
        }

        .f-bar {
            height: 100%;
            border-radius: 10px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            color: #fff;
            font-weight: 800;
            font-size: 1.05rem;
            min-width: 0;
            white-space: nowrap;
            transition: width 0.7s ease;
        }

        .f-side {
            text-align: right;
        }

        .f-side b {
            font-size: 1.15rem;
            color: var(--ink);
            display: block;
        }

        .f-side span {
            font-size: 0.8rem;
            color: var(--muted);
        }

        /* Khoảng rời đi giữa các bước */
        .f-gap {
            display: grid;
            grid-template-columns: 190px 1fr 120px;
            gap: 18px;
            align-items: center;
            margin: 6px 0;
        }

        @media (max-width: 800px) {
            .f-gap {
                grid-template-columns: 1fr;
            }
        }

        .f-gap .gap-body {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 0 6px 14px;
            border-left: 2px dashed #d1d5db;
            margin-left: 8px;
            font-size: 0.83rem;
            color: var(--muted);
        }

        .gap-pill {
            font-weight: 700;
            padding: 2px 9px;
            border-radius: 999px;
            background: #fef2f2;
            color: #b91c1c;
            font-size: 0.78rem;
        }

        .gap-ok {
            background: #f0fdf4;
            color: #166534;
        }

        /* Nhận xét */
        .insight {
            padding: 18px 22px 22px;
        }

        .insight-box {
            display: flex;
            gap: 14px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 14px 16px;
        }

        .insight-box .ic {
            font-size: 1.3rem;
            line-height: 1.2;
        }

        .insight-box b {
            color: #92400e;
            display: block;
            margin-bottom: 3px;
        }

        .insight-box p {
            margin: 0;
            color: #78350f;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        /* Bảng chi tiết */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
        }

        .detail-table th {
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--muted);
            padding: 10px 22px;
            background: #f9fafb;
            border-bottom: 1px solid var(--line);
        }

        .detail-table td {
            padding: 13px 22px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.92rem;
        }

        .detail-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .dot {
            display: inline-block;
            width: 9px;
            height: 9px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .num-r {
            text-align: right !important;
        }

        .empty {
            text-align: center;
            padding: 56px 20px;
            color: var(--muted);
        }

        .empty b {
            display: block;
            color: var(--ink);
            font-size: 1.05rem;
            margin-bottom: 6px;
        }

        .btn-link {
            display: inline-block;
            padding: 8px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            background: #fff;
        }

        .btn-link:hover {
            border-color: var(--brand);
            color: var(--brand);
        }

        @media (prefers-reduced-motion: reduce) {
            .f-bar {
                transition: none;
            }
        }

        /* ── Giao diện đồng bộ với Nhom15 Cloud (xanh dương + nền sáng) ── */
        :root {
            --blue: #2563eb;
            --blue-dark: #1d4ed8;
            --blue-soft: #eff6ff;
            --navy: #0f172a;
        }

        body {
            font-family: Inter, "Segoe UI", system-ui, -apple-system, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .main-content {
            background: #f8fafc;
        }

        .top-navbar {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
        }

        .sidebar {
            background: var(--navy);
        }

        .sidebar-brand {
            color: #fff;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .sidebar-menu li a {
            color: #94a3b8;
        }

        .sidebar-menu li a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .sidebar-menu li.active {
            background: rgba(37, 99, 235, 0.22);
            border-left-color: #3b82f6;
        }

        .sidebar-menu li.active a {
            color: #fff;
            border-left-color: #3b82f6;
        }

        .btn-primary {
            background: var(--blue);
            border-color: var(--blue);
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: var(--blue-dark);
            border-color: var(--blue-dark);
        }

        .form-control {
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="sidebar-brand">Nhom15 Analytics</div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">Thống kê & Báo cáo</a></li>
            <li class="active"><a href="funnels.php">Phân tích Chuỗi</a></li>
            <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                <li><a href="websites.php">Quản lý Website</a></li>
            <?php endif; ?>
            <li><a href="<?= $testBase ?>/site1/index.php" target="_blank">🛍️ Store</a></li>
            <li><a href="<?= $testBase ?>/site2/index.php" target="_blank">☁️ Cloud Services</a></li>
            <li><a href="../handle/logout.php" style="color: #fca5a5;">Đăng xuất (<?= h($username) ?>)</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <h2>Phân tích Chuỗi thao tác</h2>
            <div>Xin chào, <b><?= h($username) ?></b></div>
        </div>

        <div class="container-fluid">

            <div class="page-head">
                <div>
                    <h1>Hành trình đăng ký của người dùng</h1>
                    <p>Xem bao nhiêu người đi tiếp ở mỗi bước: xem trang, bấm đăng ký, gửi biểu mẫu.</p>
                </div>
                <form method="GET" class="picker">
                    <label for="website_id">Website</label>
                    <select name="website_id" id="website_id" onchange="this.form.submit()">
                        <?php foreach ($websites as $ws): ?>
                            <option value="<?= (int) $ws['id'] ?>" <?= $ws['id'] == $website_id ? 'selected' : '' ?>>
                                <?= h($ws['name']) ?> (<?= h($ws['domain']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>

            <!-- Thẻ tóm tắt -->
            <div class="stats">
                <div class="stat highlight">
                    <div class="label">Tỷ lệ chuyển đổi tổng</div>
                    <div class="num"><?= $pct_total ?>%</div>
                    <div class="sub"><?= number_format($submit) ?> người hoàn tất trên <?= number_format($view) ?> lượt
                        xem</div>
                </div>
                <div class="stat">
                    <div class="label">Tổng số phiên</div>
                    <div class="num"><?= number_format($sessions) ?></div>
                    <div class="sub">Số lần truy cập được ghi nhận</div>
                </div>
                <div class="stat">
                    <div class="label">Hoàn tất so với bấm đăng ký</div>
                    <div class="num"><?= $pct_submit ?>%</div>
                    <div class="sub"><?= number_format($submit) ?> trên <?= number_format($start) ?> người đã bấm đăng
                        ký</div>
                </div>
            </div>

            <!-- Phễu chuyển đổi -->
            <section class="panel">
                <div class="panel-head">
                    <h3>Phễu chuyển đổi</h3>
                    <a class="btn-link" href="dashboard.php?website_id=<?= $website_id ?>">Xem báo cáo chi tiết</a>
                </div>

                <?php if (!$has_data): ?>
                    <div class="empty">
                        <b>Chưa có dữ liệu cho website này</b>
                        Mở website thử nghiệm và thực hiện vài thao tác, số liệu sẽ xuất hiện ở đây.
                    </div>
                <?php else: ?>
                    <div class="funnel">
                        <div class="f-row">
                            <div class="f-title">Xem trang<small>Bước 1</small></div>
                            <div class="f-track">
                                <div class="f-bar" style="width:<?= $w1 ?>%; background:var(--c1);">
                                    <?= number_format($view) ?>
                                </div>
                            </div>
                            <div class="f-side"><b>100%</b><span>mốc bắt đầu</span></div>
                        </div>

                        <div class="f-gap">
                            <div></div>
                            <div class="gap-body">
                                <span class="gap-pill">−<?= number_format($drop1) ?> (<?= $drop1_pct ?>%)</span>
                                rời đi trước khi bấm đăng ký
                            </div>
                            <div></div>
                        </div>

                        <div class="f-row">
                            <div class="f-title">Bấm đăng ký<small>Bước 2</small></div>
                            <div class="f-track">
                                <div class="f-bar" style="width:<?= $w2 ?>%; background:var(--c2);">
                                    <?= number_format($start) ?>
                                </div>
                            </div>
                            <div class="f-side"><b><?= $pct_start ?>%</b><span>so với bước 1</span></div>
                        </div>

                        <div class="f-gap">
                            <div></div>
                            <div class="gap-body">
                                <span class="gap-pill">−<?= number_format($drop2) ?> (<?= $drop2_pct ?>%)</span>
                                bấm đăng ký nhưng không gửi form
                            </div>
                            <div></div>
                        </div>

                        <div class="f-row" style="margin-bottom:18px;">
                            <div class="f-title">Gửi biểu mẫu<small>Bước 3</small></div>
                            <div class="f-track">
                                <div class="f-bar" style="width:<?= $w3 ?>%; background:var(--c3);">
                                    <?= number_format($submit) ?>
                                </div>
                            </div>
                            <div class="f-side"><b><?= $pct_total ?>%</b><span>so với bước 1</span></div>
                        </div>
                    </div>

                    <div class="insight">
                        <div class="insight-box">
                            <div class="ic">💡</div>
                            <div>
                                <b>Điểm cần cải thiện</b>
                                <p><?= h($insight) ?>     <?= h($insight_tip) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Bảng chi tiết -->
            <section class="panel">
                <div class="panel-head">
                    <h3>Số liệu chi tiết từng bước</h3>
                </div>
                <div style="overflow-x:auto;">
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>Bước</th>
                                <th class="num-r">Số lượng</th>
                                <th class="num-r">So với bước trước</th>
                                <th class="num-r">So với bước 1</th>
                                <th class="num-r">Rời đi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="dot" style="background:var(--c1);"></span>1. Xem trang</td>
                                <td class="num-r"><b><?= number_format($view) ?></b></td>
                                <td class="num-r">–</td>
                                <td class="num-r"><?= $view > 0 ? '100%' : '0%' ?></td>
                                <td class="num-r">–</td>
                            </tr>
                            <tr>
                                <td><span class="dot" style="background:var(--c2);"></span>2. Bấm đăng ký</td>
                                <td class="num-r"><b><?= number_format($start) ?></b></td>
                                <td class="num-r"><?= $pct_start ?>%</td>
                                <td class="num-r"><?= $pct_start ?>%</td>
                                <td class="num-r"><?= number_format($drop1) ?></td>
                            </tr>
                            <tr>
                                <td><span class="dot" style="background:var(--c3);"></span>3. Gửi biểu mẫu</td>
                                <td class="num-r"><b><?= number_format($submit) ?></b></td>
                                <td class="num-r"><?= $pct_submit ?>%</td>
                                <td class="num-r"><?= $pct_total ?>%</td>
                                <td class="num-r"><?= number_format($drop2) ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

        </div>

        <footer class="main-footer" style="text-align:center; padding:20px; color:#6c757d; margin-top:30px;">
            Nhóm15 &copy; 2026
        </footer>
    </div>
</body>

</html>