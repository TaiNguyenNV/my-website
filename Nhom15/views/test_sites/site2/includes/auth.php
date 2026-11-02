<?php
// ===== Liên kết trang chính + phân quyền =====

// Ánh xạ session của trang chính (login.php) sang site2.
// Nếu login.php dùng tên khóa khác, thêm vào các danh sách dưới đây.
function s2_keys($name) {
    $k = [
        'id'    => ['user_id', 'id', 'uid', 'userid'],
        'name'  => ['username', 'user_name', 'fullname', 'full_name', 'name', 'email', 'user'],
        'email' => ['email', 'mail'],
        'role'  => ['role', 'vai_tro', 'user_role', 'level', 'type'],
    ];
    return $k[$name];
}
function s2_first($arr, $keys) {
    foreach ($keys as $k) {
        if (isset($arr[$k]) && is_scalar($arr[$k]) && trim((string)$arr[$k]) !== '') return trim((string)$arr[$k]);
    }
    return null;
}

// Trả về ['id','name','email','role'] hoặc null nếu chưa đăng nhập
function current_user() {
    static $u = false;
    if ($u !== false) return $u;
    $u = null;
    if (DEV_MODE && !empty($_SESSION['site2_dev'])) { $u = $_SESSION['site2_dev']; return $u; }

    $s = $_SESSION;
    foreach (['user', 'account', 'auth'] as $nest) {   // session dạng $_SESSION['user'] = [...]
        if (isset($s[$nest]) && is_array($s[$nest])) $s = array_merge($s, $s[$nest]);
    }
    $id   = s2_first($s, s2_keys('id'));
    $name = s2_first($s, s2_keys('name'));
    if ($id === null && $name === null) return $u;

    $raw   = strtolower((string)s2_first($s, s2_keys('role')));
    $admin = ['admin', 'administrator', 'quantri', 'quan_tri', 'superadmin', 'root'];
    $staff = ['staff', 'nhanvien', 'nhan_vien', 'support', 'manager', 'moderator', 'editor'];
    $role  = 'user';
    if (!empty($s['is_admin']) || in_array($raw, $admin, true)) $role = 'admin';
    elseif (in_array($raw, $staff, true)) $role = 'staff';

    $u = [
        'id'    => $id !== null ? $id : $name,
        'name'  => $name !== null ? $name : ('#' . $id),
        'email' => (string)s2_first($s, s2_keys('email')),
        'role'  => $role,
    ];
    return $u;
}

function role() { $u = current_user(); return $u ? $u['role'] : 'guest'; }

// Bảng phân quyền
function perms() {
    return [
        'order.create'   => ['user', 'staff', 'admin'],
        'order.view_own' => ['user', 'staff', 'admin'],
        'order.view_all' => ['staff', 'admin'],
        'order.update'   => ['admin'],
        'log.view'       => ['admin'],
        'export.orders'  => ['user', 'staff', 'admin'],
        'export.log'     => ['admin'],
    ];
}
function can($perm) { $p = perms(); return in_array(role(), $p[$perm] ?? [], true); }

// Quyền cần có để vào từng trang (trang không khai báo = công khai)
function page_perm($page) {
    $m = ['dang-ky-goi' => 'order.create', 'don-hang' => 'order.view_own', 'nhat-ky' => 'log.view'];
    return $m[$page] ?? null;
}

// ===== Liên kết sang trang chính =====
function main_login_url($back = '') {
    return MAIN_URL . MAIN_LOGIN . ($back !== '' ? '?redirect=' . urlencode($back) : '');
}
function main_home_url() { return MAIN_URL . (current_user() ? MAIN_HOME : MAIN_LOGIN); }

// ===== CSRF =====
function csrf_token() {
    if (empty($_SESSION['site2_csrf'])) $_SESSION['site2_csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['site2_csrf'];
}
function csrf_ok($t) { return !empty($_SESSION['site2_csrf']) && hash_equals($_SESSION['site2_csrf'], (string)$t); }

// ===== Đơn hàng (logs/site2-orders.json) =====
function order_statuses() {
    return ['pending' => 'Chờ thanh toán', 'paid' => 'Đã thanh toán', 'cancelled' => 'Đã hủy'];
}
function orders_file() { return LOG_DIR . '/site2-orders.json'; }
function orders_all() {
    $f = orders_file();
    if (!is_file($f)) return [];
    $d = json_decode((string)@file_get_contents($f), true);
    return is_array($d) ? $d : [];
}
function orders_mutate($fn) {
    if (!is_dir(LOG_DIR)) { @mkdir(LOG_DIR, 0775, true); }
    $fp = @fopen(orders_file(), 'c+');
    if (!$fp) return false;
    flock($fp, LOCK_EX);
    $d = json_decode((string)stream_get_contents($fp), true);
    if (!is_array($d)) $d = [];
    $d = $fn($d);
    ftruncate($fp, 0); rewind($fp);
    fwrite($fp, json_encode($d, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    fflush($fp); flock($fp, LOCK_UN); fclose($fp);
    return true;
}
function order_add($o) { return orders_mutate(function ($d) use ($o) { $d[] = $o; return $d; }); }
function order_set_status($code, $status) {
    return orders_mutate(function ($d) use ($code, $status) {
        foreach ($d as &$o) { if (($o['code'] ?? '') === $code) $o['status'] = $status; }
        return $d;
    });
}

// ===== Tải dữ liệu =====
// Xuất CSV (UTF-8 có BOM để Excel đọc đúng tiếng Việt, chặn CSV injection)
function csv_cell($v) {
    $v = (string)$v;
    if ($v !== '' && strpos("=+-@\t\r", $v[0]) !== false) $v = "'" . $v;
    return $v;
}
function send_csv($filename, $header, $rows) {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('X-Content-Type-Options: nosniff');
    echo "\xEF\xBB\xBF";
    $out = fopen('php://output', 'w');
    fputcsv($out, $header);
    foreach ($rows as $r) fputcsv($out, array_map('csv_cell', $r));
    fclose($out);
}
// Danh sách tài liệu trong thư mục downloads/ (chỉ file trực tiếp, bỏ file ẩn)
function doc_list() {
    $out = [];
    if (!is_dir(DOWNLOAD_DIR)) return $out;
    foreach (scandir(DOWNLOAD_DIR) as $f) {
        $p = DOWNLOAD_DIR . '/' . $f;
        if ($f[0] === '.' || !is_file($p)) continue;
        $out[] = ['name' => $f, 'size' => filesize($p), 'time' => filemtime($p)];
    }
    return $out;
}
function fmt_size($b) {
    if ($b >= 1048576) return round($b / 1048576, 1) . ' MB';
    if ($b >= 1024) return round($b / 1024) . ' KB';
    return $b . ' B';
}