<?php
require __DIR__ . '/includes/config.php';
if (SESSION_NAME !== '') { session_name(SESSION_NAME); }
session_start();   // dùng chung session với trang chính (cùng domain/port)
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/tracker-log.php';

// Chế độ thử vai trò (chỉ khi DEV_MODE = true)
if (DEV_MODE && isset($_GET['dev_role'])) {
    $r = $_GET['dev_role'];
    if (in_array($r, ['admin', 'staff', 'user'], true)) {
        $_SESSION['site2_dev'] = ['id' => 'dev-' . $r, 'name' => 'Dev ' . $r, 'email' => 'dev@local', 'role' => $r];
    } else {
        unset($_SESSION['site2_dev']);
    }
    header('Location: index.php'); exit;
}

// Ghi nhận click từ JavaScript
if (($_GET['action'] ?? '') === 'track' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    track_event($_POST['type'] ?? 'click', $_POST['detail'] ?? '');
    http_response_code(204); exit;
}

// Đăng xuất (xóa session dùng chung nên cũng đăng xuất khỏi trang chính)
if (($_GET['action'] ?? '') === 'logout') {
    track_event('logout');
    $_SESSION = [];
    session_destroy();
    header('Location: ' . MAIN_URL . MAIN_LOGIN); exit;
}

// ===== Tải dữ liệu: ?action=download&type=bang-gia|don-hang|nhat-ky|tai-lieu =====
if (($_GET['action'] ?? '') === 'download') {
    $type = $_GET['type'] ?? '';
    if ($type === 'bang-gia') {
        global $PACKAGES;
        $rows = [];
        foreach ($PACKAGES as $p) {
            $rows[] = [$p['name'], $p['specs']['CPU'], $p['specs']['RAM'], $p['specs']['SSD'], $p['specs']['Băng thông'], $p['specs']['Website'], $p['price']];
        }
        track_event('download', 'bang-gia');
        send_csv('bang-gia.csv', ['Gói', 'CPU', 'RAM', 'SSD', 'Băng thông', 'Website', 'Giá/tháng (VND)'], $rows);
        exit;
    }
    if (in_array($type, ['don-hang', 'nhat-ky', 'tai-lieu'], true) && !current_user()) {
        header('Location: ' . main_login_url($_SERVER['REQUEST_URI'] ?? '')); exit;
    }
    if ($type === 'don-hang' && can('export.orders')) {
        $me = current_user();
        $st = order_statuses();
        $rows = [];
        foreach (orders_all() as $o) {
            if (!can('order.view_all') && (string)($o['user_id'] ?? '') !== (string)$me['id']) continue;
            $s = $o['status'] ?? 'pending'; if ($s === 'active') $s = 'paid';
            $rows[] = [$o['code'] ?? '', $o['time'] ?? '', $o['name'] ?? '', $o['email'] ?? '', $o['phone'] ?? '',
                       $o['package'] ?? '', (int)($o['cycle'] ?? 1), (int)($o['total'] ?? 0), $o['method'] ?? '', $st[$s] ?? $s];
        }
        track_event('download', 'don-hang');
        send_csv('don-hang.csv', ['Mã đơn', 'Thời gian', 'Khách hàng', 'Email', 'SĐT', 'Gói', 'Chu kỳ (tháng)', 'Tổng tiền (VND)', 'Thanh toán', 'Trạng thái'], $rows);
        exit;
    }
    if ($type === 'nhat-ky' && can('export.log')) {
        $rows = [];
        $f = LOG_DIR . '/site2-tracker.log';
        foreach (is_file($f) ? file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [] as $ln) {
            $r = json_decode($ln, true);
            if (is_array($r)) $rows[] = [$r['time'] ?? '', $r['user'] ?? '', $r['role'] ?? '', $r['type'] ?? '', $r['detail'] ?? '', $r['ip'] ?? ''];
        }
        track_event('download', 'nhat-ky');
        send_csv('nhat-ky.csv', ['Thời gian', 'Người dùng', 'Vai trò', 'Sự kiện', 'Chi tiết', 'IP'], $rows);
        exit;
    }
    if ($type === 'tai-lieu') {
        $name = basename((string)($_GET['file'] ?? ''));   // basename chặn ../
        $ok = false;
        foreach (doc_list() as $d) { if ($d['name'] === $name) { $ok = true; break; } }
        if ($ok) {
            $path = DOWNLOAD_DIR . '/' . $name;
            $isTxt = strtolower(pathinfo($name, PATHINFO_EXTENSION)) === 'txt';
            $view  = $isTxt && !empty($_GET['view']);   // ?view=1: xem ngay trên trình duyệt (chỉ file .txt)
            track_event($view ? 'view' : 'download', 'tai-lieu: ' . $name);
            header($view ? 'Content-Type: text/plain; charset=UTF-8' : 'Content-Type: application/octet-stream');
            header('Content-Disposition: ' . ($view ? 'inline' : 'attachment') . '; filename="' . str_replace('"', '', $name) . '"');
            header('Content-Length: ' . filesize($path));
            header('X-Content-Type-Options: nosniff');
            readfile($path);
            exit;
        }
    }
    http_response_code(403);   // sai loại, không đủ quyền hoặc file không tồn tại
    exit('Không thể tải: không đủ quyền hoặc file không tồn tại.');
}

$page = $_GET['page'] ?? 'trang-chu';
if (!isset($PAGES[$page])) { $page = 'trang-chu'; }

// Kiểm tra quyền truy cập trang
$denied = false;
$need = page_perm($page);
if ($need && !can($need)) {
    if (!current_user()) {   // chưa đăng nhập -> sang đăng nhập của trang chính
        header('Location: ' . main_login_url($_SERVER['REQUEST_URI'] ?? ''));
        exit;
    }
    http_response_code(403);
    $denied = true;
    track_event('denied', $page);
}

// Admin đổi trạng thái đơn hàng
if (!$denied && $page === 'don-hang' && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['form'] ?? '') === 'status') {
    $st = $_POST['status'] ?? '';
    if (can('order.update') && csrf_ok($_POST['csrf'] ?? '') && isset(order_statuses()[$st])) {
        order_set_status($_POST['code'] ?? '', $st);
        track_event('order_status', ($_POST['code'] ?? '') . ' -> ' . $st);
    }
    header('Location: index.php?page=don-hang'); exit;
}

if (!$denied) { track_event('pageview', $page); }

include __DIR__ . '/includes/header.php';
include __DIR__ . '/pages/' . ($denied ? '403' : $page) . '.php';
include __DIR__ . '/includes/footer.php';