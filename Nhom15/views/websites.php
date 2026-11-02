<?php
// views/websites.php
session_start();
require_once '../database/db_connection.php';
require_once '../functions/auth_functions.php';

// Chỉ cho phép Quản trị viên (Admin) truy cập trang này
requireAdmin();

// Đường dẫn tới thư mục test_sites (đổi thành 'test_sites' nếu thư mục nằm trong views/)
$testBase = 'test_sites';

// Token chống giả mạo yêu cầu cho thao tác sửa / xóa
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf'];

// ── Xử lý sửa / xóa website (gửi POST về chính trang này) ──
function redirect_to($key, $value)
{
    header('Location: websites.php?' . $key . '=' . urlencode($value));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if (!hash_equals($csrf, (string) ($_POST['csrf'] ?? ''))) {
        redirect_to('error', 'Phiên làm việc không hợp lệ, hãy tải lại trang và thử lại.');
    }

    $id = intval($_POST['id'] ?? 0);

    if ($action === 'update') {
        $name = trim($_POST['name'] ?? '');
        $domain = trim($_POST['domain'] ?? '');
        $users = trim($_POST['allowed_users'] ?? '');

        if ($id <= 0 || $name === '' || $domain === '' || $users === '') {
            redirect_to('error', 'Vui lòng điền đầy đủ tên, tên miền và danh sách thành viên.');
        }

        // Chuẩn hóa domain và danh sách thành viên
        $domain = rtrim(preg_replace('#^https?://#i', '', $domain), '/');
        $list = array_values(array_unique(array_filter(array_map('trim', explode(',', $users)))));
        if (empty($list)) {
            redirect_to('error', 'Cần ít nhất một thành viên được xem báo cáo.');
        }
        $users = implode(',', $list);

        try {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM websites WHERE domain = ? AND id <> ?");
            $stmt->execute([$domain, $id]);
            if ($stmt->fetchColumn() > 0) {
                redirect_to('error', 'Tên miền này đã được dùng cho website khác.');
            }
            $stmt = $pdo->prepare("UPDATE websites SET name = ?, domain = ?, allowed_users = ? WHERE id = ?");
            $stmt->execute([$name, $domain, $users, $id]);
        } catch (PDOException $e) {
            redirect_to('error', 'Lỗi cơ sở dữ liệu khi cập nhật.');
        }
        redirect_to('success', 'updated');
    }

    if ($action === 'delete') {
        if ($id <= 0) {
            redirect_to('error', 'Không xác định được website cần xóa.');
        }
        try {
            $stmt = $pdo->prepare("DELETE FROM websites WHERE id = ?");
            $stmt->execute([$id]);
            if ($stmt->rowCount() === 0) {
                redirect_to('error', 'Website không tồn tại hoặc đã bị xóa.');
            }
        } catch (PDOException $e) {
            // Thường do còn dữ liệu liên quan (events, funnels...) ràng buộc khóa ngoại
            redirect_to('error', 'Không xóa được vì website còn dữ liệu liên quan.');
        }
        redirect_to('success', 'deleted');
    }
}

// ── Hàm hỗ trợ (không cần extension mbstring) ──
function h($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

function initial($s)
{
    $s = trim((string) $s);
    if ($s === '') {
        return '?';
    }
    $c = preg_match('/^./u', $s, $m) ? $m[0] : substr($s, 0, 1);
    return function_exists('mb_strtoupper') ? mb_strtoupper($c, 'UTF-8') : strtoupper($c);
}

function lower($s)
{
    return function_exists('mb_strtolower') ? mb_strtolower((string) $s, 'UTF-8') : strtolower((string) $s);
}

$username = $_SESSION['user'] ?? 'admin';

$websites = $pdo->query("SELECT * FROM websites ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
$success = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

$success_messages = [
    'updated' => 'Đã cập nhật website.',
    'deleted' => 'Đã xóa website.',
];
$success_text = $success ? ($success_messages[$success] ?? 'Đã thêm website thành công.') : '';

$members_by_site = [];
$all_members = [];
foreach ($websites as $ws) {
    $list = array_values(array_filter(array_map('trim', explode(',', (string) ($ws['allowed_users'] ?? '')))));
    $members_by_site[$ws['id']] = $list;
    foreach ($list as $m) {
        $all_members[$m] = true;
    }
}

$palette = ['#2563eb', '#0284c7', '#16a34a', '#d97706', '#7e22ce', '#e11d48', '#0d9488'];
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Website - Nhom15 Analytics</title>
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
            --brand-soft: #eff6ff;
            --danger: #dc2626;
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

        .summary {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .summary-item {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 8px 14px;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .summary-item b {
            color: var(--ink);
            font-size: 1.05rem;
            margin-right: 4px;
        }

        .notice {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 0.92rem;
            border: 1px solid;
        }

        .notice-ok {
            background: #f0fdf4;
            color: #166534;
            border-color: #bbf7d0;
        }

        .notice-err {
            background: #fef2f2;
            color: #991b1b;
            border-color: #fecaca;
        }

        .notice button {
            background: none;
            border: 0;
            font-size: 1.2rem;
            line-height: 1;
            cursor: pointer;
            color: inherit;
            opacity: 0.6;
        }

        .notice button:hover {
            opacity: 1;
        }

        .layout {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 360px;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1000px) {
            .layout {
                grid-template-columns: 1fr;
            }
        }

        .panel {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
        }

        .panel-head h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink);
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 8px 12px 8px 32px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.88rem;
            width: 220px;
            max-width: 100%;
            box-sizing: border-box;
        }

        .search-box svg {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .ws-table {
            width: 100%;
            border-collapse: collapse;
        }

        .ws-table th {
            text-align: left;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--muted);
            padding: 10px 20px;
            background: #f9fafb;
            border-bottom: 1px solid var(--line);
        }

        .ws-table td {
            padding: 14px 20px;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        .ws-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .ws-table tbody tr:hover {
            background: #f8fbff;
        }

        .ws-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .ws-avatar {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            color: #fff;
            font-weight: 800;
            display: grid;
            place-items: center;
            flex-shrink: 0;
        }

        .ws-name {
            font-weight: 700;
            color: var(--ink);
        }

        .ws-domain {
            font-size: 0.83rem;
            color: var(--muted);
        }

        .id-pill {
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 0.8rem;
            background: #f3f4f6;
            color: #374151;
            border: 0;
            padding: 3px 8px;
            border-radius: 6px;
            cursor: pointer;
        }

        .id-pill:hover {
            background: var(--brand-soft);
            color: var(--brand);
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: var(--brand-soft);
            color: #1e40af;
            font-size: 0.78rem;
            font-weight: 600;
            padding: 3px 9px 3px 4px;
            border-radius: 999px;
        }

        .chip i {
            font-style: normal;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: var(--brand);
            color: #fff;
            font-size: 0.65rem;
            display: grid;
            place-items: center;
        }

        /* Nút thao tác */
        .actions {
            display: flex;
            gap: 6px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .btn-sm {
            display: inline-block;
            padding: 6px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            background: #fff;
            white-space: nowrap;
            cursor: pointer;
            font-family: inherit;
        }

        .btn-sm:hover {
            border-color: var(--brand);
            color: var(--brand);
        }

        .btn-sm.danger:hover {
            border-color: var(--danger);
            color: var(--danger);
            background: #fef2f2;
        }

        .btn-sm:focus-visible {
            outline: 2px solid var(--brand);
            outline-offset: 2px;
        }

        .empty {
            text-align: center;
            padding: 48px 20px;
            color: var(--muted);
        }

        .empty b {
            display: block;
            color: var(--ink);
            margin-bottom: 4px;
        }

        /* Form */
        .form-body {
            padding: 20px;
        }

        .field {
            margin-bottom: 16px;
        }

        .field label {
            display: block;
            font-size: 0.88rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .field input {
            width: 100%;
            box-sizing: border-box;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.92rem;
        }

        .field .hint {
            font-size: 0.78rem;
            color: var(--muted);
            margin-top: 5px;
        }

        .field input:focus,
        .search-box input:focus {
            outline: 2px solid var(--brand);
            outline-offset: 1px;
            border-color: var(--brand);
        }

        .chip-preview {
            margin-top: 8px;
            min-height: 22px;
        }

        .submit-btn {
            width: 100%;
            padding: 11px;
            border: 0;
            border-radius: 8px;
            background: var(--brand);
            color: #fff;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }

        .submit-btn:focus-visible {
            outline: 2px solid var(--brand);
            outline-offset: 2px;
        }

        .submit-btn.danger {
            background: var(--danger);
        }

        .submit-btn.danger:hover {
            background: #b91c1c;
        }

        /* Hộp thoại sửa / xóa */
        dialog {
            border: 0;
            border-radius: 14px;
            padding: 0;
            width: 440px;
            max-width: calc(100vw - 32px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
        }

        dialog::backdrop {
            background: rgba(17, 24, 39, 0.5);
        }

        .dlg-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid var(--line);
        }

        .dlg-head h3 {
            margin: 0;
            font-size: 1.05rem;
            color: var(--ink);
        }

        .dlg-close {
            background: none;
            border: 0;
            font-size: 1.4rem;
            line-height: 1;
            cursor: pointer;
            color: var(--muted);
        }

        .dlg-foot {
            display: flex;
            gap: 10px;
            margin-top: 4px;
        }

        .dlg-foot .submit-btn {
            flex: 1;
        }

        .cancel-btn {
            flex: 1;
            padding: 11px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #fff;
            font-weight: 600;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .cancel-btn:hover {
            background: #f9fafb;
        }

        .dlg-text {
            color: #374151;
            font-size: 0.93rem;
            line-height: 1.5;
            margin: 0 0 16px;
        }

        @media (max-width: 700px) {

            .ws-table th:nth-child(2),
            .ws-table td:nth-child(2) {
                display: none;
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
            <li><a href="funnels.php">Phân tích Chuỗi</a></li>
            <li class="active"><a href="websites.php">Quản lý Website</a></li>
            <li><a href="<?= $testBase ?>/site1/index.php" target="_blank">🛍️ Store</a></li>
            <li><a href="<?= $testBase ?>/site2/index.php" target="_blank">☁️ Cloud Services</a></li>
            <li><a href="../handle/logout.php" style="color: #fca5a5;">Đăng xuất (<?= h($username) ?>)</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="top-navbar">
            <h2>Quản lý Website</h2>
            <div>Xin chào, <b><?= h($username) ?></b></div>
        </div>

        <div class="container-fluid">

            <div class="page-head">
                <div>
                    <h1>Website đang theo dõi</h1>
                    <p>Đăng ký, chỉnh sửa hoặc xóa website và chọn thành viên được xem báo cáo.</p>
                </div>
                <div class="summary">
                    <div class="summary-item"><b><?= count($websites) ?></b>website</div>
                    <div class="summary-item"><b><?= count($all_members) ?></b>thành viên</div>
                </div>
            </div>

            <?php if ($success_text): ?>
                <div class="notice notice-ok" role="status">
                    <span><?= h($success_text) ?></span>
                    <button type="button" aria-label="Đóng thông báo" onclick="this.parentElement.remove()">&times;</button>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="notice notice-err" role="alert">
                    <span><?= h($error) ?></span>
                    <button type="button" aria-label="Đóng thông báo" onclick="this.parentElement.remove()">&times;</button>
                </div>
            <?php endif; ?>

            <div class="layout">

                <!-- Danh sách website -->
                <section class="panel">
                    <div class="panel-head">
                        <h3>Danh sách website</h3>
                        <div class="search-box">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#9ca3af"
                                stroke-width="2.5">
                                <circle cx="11" cy="11" r="7" />
                                <path stroke-linecap="round" d="M20 20l-3.5-3.5" />
                            </svg>
                            <input type="search" id="wsSearch" placeholder="Tìm theo tên hoặc domain"
                                aria-label="Tìm website">
                        </div>
                    </div>

                    <?php if (empty($websites)): ?>
                        <div class="empty">
                            <b>Chưa có website nào</b>
                            Thêm website đầu tiên ở biểu mẫu bên cạnh để bắt đầu thu thập số liệu.
                        </div>
                    <?php else: ?>
                        <div style="overflow-x:auto;">
                            <table class="ws-table" id="wsTable">
                                <thead>
                                    <tr>
                                        <th>Website</th>
                                        <th>ID</th>
                                        <th>Thành viên có quyền</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($websites as $ws):
                                        $id = (int) $ws['id'];
                                        $color = $palette[$id % count($palette)];
                                        $members = $members_by_site[$ws['id']] ?? [];
                                        ?>
                                        <tr data-search="<?= h(lower($ws['name'] . ' ' . $ws['domain'])) ?>">
                                            <td>
                                                <div class="ws-cell">
                                                    <div class="ws-avatar" style="background:<?= $color ?>;">
                                                        <?= h(initial($ws['name'])) ?>
                                                    </div>
                                                    <div>
                                                        <div class="ws-name"><?= h($ws['name']) ?></div>
                                                        <div class="ws-domain"><?= h($ws['domain']) ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="id-pill" title="Bấm để sao chép ID"
                                                    onclick="copyId(this, '<?= $id ?>')">#<?= $id ?></button>
                                            </td>
                                            <td>
                                                <div class="chips">
                                                    <?php foreach ($members as $m): ?>
                                                        <span class="chip"><i><?= h(initial($m)) ?></i><?= h($m) ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="actions">
                                                    <a class="btn-sm" href="dashboard.php?website_id=<?= $id ?>">Xem báo cáo</a>
                                                    <button type="button" class="btn-sm js-edit" data-id="<?= $id ?>"
                                                        data-name="<?= h($ws['name']) ?>" data-domain="<?= h($ws['domain']) ?>"
                                                        data-users="<?= h($ws['allowed_users']) ?>">Sửa</button>
                                                    <button type="button" class="btn-sm danger js-delete" data-id="<?= $id ?>"
                                                        data-name="<?= h($ws['name']) ?>">Xóa</button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <div class="empty" id="noResult" style="display:none;">
                                <b>Không tìm thấy website phù hợp</b>
                                Thử tìm bằng tên hoặc domain khác.
                            </div>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Form thêm website -->
                <section class="panel">
                    <div class="panel-head">
                        <h3>Thêm website mới</h3>
                    </div>
                    <form class="form-body" action="../handle/website_process.php" method="POST">
                        <div class="field">
                            <label for="name">Tên website</label>
                            <input type="text" id="name" name="name" required maxlength="100"
                                placeholder="Ví dụ: Shop Giày Online">
                        </div>

                        <div class="field">
                            <label for="domain">Tên miền</label>
                            <input type="text" id="domain" name="domain" required maxlength="255"
                                placeholder="Ví dụ: shopgiay.local">
                            <div class="hint">Chỉ nhập tên miền, không cần http:// hoặc dấu / ở cuối.</div>
                        </div>

                        <div class="field">
                            <label for="allowed_users">Thành viên được xem báo cáo</label>
                            <input type="text" id="allowed_users" name="allowed_users" required value="admin,manager1"
                                placeholder="admin,manager1" data-preview="chipPreview">
                            <div class="hint">Nhập tên đăng nhập, phân cách bằng dấu phẩy.</div>
                            <div class="chips chip-preview" id="chipPreview" aria-live="polite"></div>
                        </div>

                        <button type="submit" class="submit-btn">Đăng ký website</button>
                    </form>
                </section>

            </div>
        </div>

        <footer class="main-footer" style="text-align:center; padding:20px; color:#6c757d; margin-top:30px;">
            Nhóm15 &copy; 2026
        </footer>
    </div>

    <!-- Hộp thoại sửa website -->
    <dialog id="editDialog" aria-labelledby="editTitle">
        <div class="dlg-head">
            <h3 id="editTitle">Sửa website</h3>
            <button type="button" class="dlg-close" aria-label="Đóng" data-close>&times;</button>
        </div>
        <form class="form-body" action="websites.php" method="POST">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
            <input type="hidden" name="id" id="edit_id">

            <div class="field">
                <label for="edit_name">Tên website</label>
                <input type="text" id="edit_name" name="name" required maxlength="100">
            </div>
            <div class="field">
                <label for="edit_domain">Tên miền</label>
                <input type="text" id="edit_domain" name="domain" required maxlength="255">
            </div>
            <div class="field">
                <label for="edit_users">Thành viên được xem báo cáo</label>
                <input type="text" id="edit_users" name="allowed_users" required data-preview="editChips">
                <div class="hint">Phân cách bằng dấu phẩy.</div>
                <div class="chips chip-preview" id="editChips"></div>
            </div>

            <div class="dlg-foot">
                <button type="button" class="cancel-btn" data-close>Hủy</button>
                <button type="submit" class="submit-btn">Lưu thay đổi</button>
            </div>
        </form>
    </dialog>

    <!-- Hộp thoại xác nhận xóa -->
    <dialog id="deleteDialog" aria-labelledby="deleteTitle">
        <div class="dlg-head">
            <h3 id="deleteTitle">Xóa website</h3>
            <button type="button" class="dlg-close" aria-label="Đóng" data-close>&times;</button>
        </div>
        <form class="form-body" action="websites.php" method="POST">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="csrf" value="<?= h($csrf) ?>">
            <input type="hidden" name="id" id="delete_id">
            <p class="dlg-text">Bạn sắp xóa website <b id="delete_name"></b>. Website sẽ không còn trong danh sách theo
                dõi và hành động này không thể hoàn tác.</p>
            <div class="dlg-foot">
                <button type="button" class="cancel-btn" data-close>Giữ lại</button>
                <button type="submit" class="submit-btn danger">Xóa website</button>
            </div>
        </form>
    </dialog>

    <script>
        // Tìm kiếm nhanh
        var search = document.getElementById('wsSearch');
        if (search) {
            search.addEventListener('input', function () {
                var q = search.value.trim().toLowerCase();
                var shown = 0;
                document.querySelectorAll('#wsTable tbody tr').forEach(function (tr) {
                    var hit = tr.dataset.search.indexOf(q) !== -1;
                    tr.style.display = hit ? '' : 'none';
                    if (hit) shown++;
                });
                var nr = document.getElementById('noResult');
                if (nr) nr.style.display = shown ? 'none' : 'block';
            });
        }

        // Xem trước danh sách thành viên dưới mỗi ô nhập
        function renderChips(input) {
            var box = document.getElementById(input.dataset.preview);
            if (!box) return;
            box.textContent = '';
            input.value.split(',').map(function (s) { return s.trim(); }).filter(Boolean).forEach(function (u) {
                var chip = document.createElement('span');
                chip.className = 'chip';
                var i = document.createElement('i');
                i.textContent = u.charAt(0).toUpperCase();
                chip.appendChild(i);
                chip.appendChild(document.createTextNode(u));
                box.appendChild(chip);
            });
        }
        document.querySelectorAll('input[data-preview]').forEach(function (inp) {
            inp.addEventListener('input', function () { renderChips(inp); });
            renderChips(inp);
        });

        // Hộp thoại sửa / xóa
        var editDlg = document.getElementById('editDialog');
        var deleteDlg = document.getElementById('deleteDialog');

        document.querySelectorAll('.js-edit').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('edit_id').value = btn.dataset.id;
                document.getElementById('edit_name').value = btn.dataset.name;
                document.getElementById('edit_domain').value = btn.dataset.domain;
                var users = document.getElementById('edit_users');
                users.value = btn.dataset.users;
                renderChips(users);
                editDlg.showModal();
            });
        });

        document.querySelectorAll('.js-delete').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById('delete_id').value = btn.dataset.id;
                document.getElementById('delete_name').textContent = btn.dataset.name;
                deleteDlg.showModal();
            });
        });

        // Đóng hộp thoại: nút đóng/hủy hoặc bấm ra ngoài vùng hộp thoại
        document.querySelectorAll('dialog').forEach(function (dlg) {
            dlg.querySelectorAll('[data-close]').forEach(function (b) {
                b.addEventListener('click', function () { dlg.close(); });
            });
            dlg.addEventListener('click', function (e) {
                if (e.target === dlg) dlg.close();
            });
        });

        // Sao chép ID website
        function copyId(btn, id) {
            var old = btn.textContent;
            function done() { btn.textContent = 'Đã chép'; setTimeout(function () { btn.textContent = old; }, 1200); }
            if (navigator.clipboard) {
                navigator.clipboard.writeText(id).then(done);
            } else {
                done();
            }
        }
    </script>
</body>

</html>