<?php
define('SITE_NAME', 'Nhom15 Cloud');
define('ASSET_CSS', 'assets/asstes/css/site2-style.css');
define('ASSET_JS', 'assets/asstes/css/js/site2-app.js');
define('LOG_DIR', dirname(__DIR__) . '/logs');
define('DOWNLOAD_DIR', dirname(__DIR__) . '/downloads');   // đặt file tài liệu (pdf, docx, zip...) vào đây

// ===== Liên kết với trang chính (Nhom15/views/) =====
// Tự tính: .../views/test_sites/site2/index.php  ->  .../views/
// Nếu tính sai, gán tay trước dòng này, ví dụ: define('MAIN_URL', '/Nhom15/views/');
if (!defined('MAIN_URL')) {
    $__b = str_replace('\\', '/', dirname(dirname(dirname($_SERVER['SCRIPT_NAME'] ?? '/'))));
    define('MAIN_URL', rtrim($__b, '/') . '/');
}
define('MAIN_LOGIN', 'login.php');
define('MAIN_REGISTER', 'register.php');
define('MAIN_HOME', 'dashboard.php');   // trang chính sau khi đăng nhập
define('SESSION_NAME', '');             // nếu trang chính dùng session_name('xxx') thì điền đúng tên đó
define('DEV_MODE', false);              // true = thử vai trò bằng ?dev_role=admin|staff|user|guest (CHỈ DÙNG KHI TEST)

// Thông tin nhận chuyển khoản (đổi thành thông tin thật của nhóm)
define('BANK_NAME', 'Vietcombank');
define('BANK_ACC', '0123456789');
define('BANK_OWNER', 'CONG TY NHOM15 CLOUD');

$PAGES = [
    'trang-chu'       => 'Trang chủ',
    'dich-vu-cloud'   => 'Dịch vụ Cloud',
    'bang-gia'        => 'Bảng giá',
    'ho-tro-ky-thuat' => 'Hỗ trợ kỹ thuật',
    'tai-lieu'        => 'Tải dữ liệu',
    'don-hang'        => 'Đơn hàng',
    'nhat-ky'         => 'Nhật ký hệ thống',
    'dang-ky-goi'     => 'Đăng ký gói',
];

$PACKAGES = [
    'starter' => [
        'name' => 'Starter', 'price' => 99000, 'tag' => 'Cho website cá nhân',
        'desc' => 'Gói khởi đầu cho blog, portfolio và website giới thiệu nhỏ.',
        'specs' => ['CPU' => '1 vCPU', 'RAM' => '1 GB', 'SSD' => '20 GB NVMe', 'Băng thông' => '500 GB/tháng', 'Website' => '1 website'],
        'features' => ['SSL miễn phí', 'Backup hàng tuần', 'Cài WordPress 1-click', 'Hỗ trợ email 24/7'],
        'best' => 'Blog, portfolio, landing page',
    ],
    'business' => [
        'name' => 'Business', 'price' => 249000, 'tag' => 'Phổ biến nhất', 'hot' => true,
        'desc' => 'Tài nguyên mạnh hơn cho doanh nghiệp nhỏ, cửa hàng online và website có lượng truy cập ổn định.',
        'specs' => ['CPU' => '2 vCPU', 'RAM' => '4 GB', 'SSD' => '60 GB NVMe', 'Băng thông' => 'Không giới hạn', 'Website' => '10 website'],
        'features' => ['SSL miễn phí', 'Backup hàng ngày', 'CDN toàn cầu', 'Chống DDoS cơ bản', 'Hỗ trợ chat 24/7'],
        'best' => 'Cửa hàng online, website công ty',
    ],
    'pro' => [
        'name' => 'Pro', 'price' => 599000, 'tag' => 'Hiệu năng cao',
        'desc' => 'Máy chủ ảo hiệu năng cao với quyền root, phù hợp ứng dụng web và hệ thống phức tạp.',
        'specs' => ['CPU' => '4 vCPU', 'RAM' => '8 GB', 'SSD' => '160 GB NVMe', 'Băng thông' => 'Không giới hạn', 'Website' => 'Không giới hạn'],
        'features' => ['Quyền root đầy đủ', 'Backup 6 giờ/lần', 'Chống DDoS nâng cao', 'IP riêng', 'Giám sát 24/7', 'Hỗ trợ ưu tiên'],
        'best' => 'Ứng dụng web, API, thương mại điện tử lớn',
    ],
    'enterprise' => [
        'name' => 'Enterprise', 'price' => 1499000, 'tag' => 'Doanh nghiệp lớn',
        'desc' => 'Hạ tầng riêng, SLA 99.99% và đội ngũ kỹ thuật hỗ trợ chuyên biệt cho hệ thống quan trọng.',
        'specs' => ['CPU' => '8+ vCPU', 'RAM' => '16+ GB', 'SSD' => '500 GB NVMe', 'Băng thông' => 'Không giới hạn', 'Website' => 'Không giới hạn'],
        'features' => ['SLA 99.99%', 'Cân bằng tải', 'Backup liên tục', 'WAF & chống DDoS cao cấp', 'Quản lý tài khoản riêng', 'Hotline 24/7'],
        'best' => 'Ngân hàng, tài chính, hệ thống lớn',
    ],
];

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function vnd($n) { return number_format($n, 0, ',', '.') . 'đ'; }
// Cắt chuỗi, không cần php-mbstring
function cut($s, $n) {
    $s = (string)$s;
    return function_exists('mb_substr') ? mb_substr($s, 0, $n) : substr($s, 0, $n);
}
// Tính tiền: trả theo năm giảm 20%, VAT 10%
function calc_total($price, $cycle) {
    $base = $price * $cycle;
    $disc = $cycle === 12 ? (int)round($base * 0.2) : 0;
    $sub  = $base - $disc;
    $vat  = (int)round($sub * 0.1);
    return ['base' => $base, 'disc' => $disc, 'vat' => $vat, 'total' => $sub + $vat];
}