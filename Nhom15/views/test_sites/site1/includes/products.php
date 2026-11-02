<?php
// views/test_sites/site1/includes/products.php
// Dữ liệu + hàm dùng chung cho index.php (danh sách) và product.php (chi tiết)

const PRODUCTS_PER_PAGE = 4;          // số sản phẩm mỗi trang (đổi tại đây)
const DEFAULT_CATEGORY  = 'thoitrang';

// Ảnh dự phòng khi ảnh Unsplash không tải được (không chứa dấu nháy kép/đơn để nhúng vào onerror)
const SITE1_IMG_FALLBACK = 'data:image/svg+xml;utf8,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20width=%27600%27%20height=%27400%27%3E%3Crect%20width=%27100%25%27%20height=%27100%25%27%20fill=%27%23f1f5f9%27/%3E%3Ctext%20x=%2750%25%27%20y=%2750%25%27%20font-size=%2764%27%20text-anchor=%27middle%27%20dominant-baseline=%27middle%27%3E%F0%9F%93%A6%3C/text%3E%3C/svg%3E';

function site1_unsplash($id)
{
    return "https://images.unsplash.com/{$id}?w=600&auto=format&fit=crop&q=80";
}

/** Chỉ tài khoản có role = admin (giống dashboard.php) mới được xem Live Activity Log. */
function site1_is_admin()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// ---------------------------------------------------------------- DANH MỤC
$topCategories = [
    'thoitrang' => ['label' => '👔 Đồ dùng Thời trang', 'title' => 'Đồ dùng Thời trang'],
    'dientu'    => ['label' => '⚡ Thiết bị Điện tử',    'title' => 'Thiết bị Điện tử'],
];

$subCategories = [
    'thoitrang' => [
        ['id' => 'all',           'name' => 'Tất cả',    'icon' => ''],
        ['id' => 'giay',          'name' => 'Giày dép',  'icon' => '👟'],
        ['id' => 'aosomi',        'name' => 'Áo sơ mi',  'icon' => '👕'],
        ['id' => 'aokhoac',       'name' => 'Áo khoác',  'icon' => '🧥'],
        ['id' => 'quanjeans',     'name' => 'Quần jeans', 'icon' => '👖'],
        ['id' => 'balo',          'name' => 'Balo & Túi', 'icon' => '🎒'],
        ['id' => 'kinhmat',       'name' => 'Kính mát',  'icon' => '🕶️'],
        ['id' => 'munon',         'name' => 'Mũ nón',    'icon' => '🧢'],
        ['id' => 'phukiencanhan', 'name' => 'Phụ kiện',  'icon' => '🧴'],
    ],
    'dientu' => [
        ['id' => 'all',           'name' => 'Tất cả',             'icon' => ''],
        ['id' => 'laptop',        'name' => 'Laptop',             'icon' => '💻'],
        ['id' => 'dienthoai',     'name' => 'Điện thoại',         'icon' => '📱'],
        ['id' => 'dongho',        'name' => 'Đồng hồ thông minh', 'icon' => '⌚'],
        ['id' => 'amthanh',       'name' => 'Tai nghe & Loa',     'icon' => '🎧'],
        ['id' => 'chuot_banphim', 'name' => 'Chuột & Phím',       'icon' => '⌨️'],
        ['id' => 'sac_cucap',     'name' => 'Củ cáp & Sạc',       'icon' => '🔌'],
        ['id' => 'camera',        'name' => 'Máy ảnh & Camera',   'icon' => '📷'],
    ],
];

// ---------------------------------------------------------------- SẢN PHẨM
$products = [
    // ===== THỜI TRANG =====
    ['id' => 1,  'cat' => 'thoitrang', 'sub' => 'giay',          'tag' => 'Giày dép',   'name' => 'Giày Thể Thao Nam Nike Air',            'price' => 1250000,  'img' => site1_unsplash('photo-1542291026-7eec264c27ff'), 'desc' => 'Giày thể thao nam đế êm, thân giày dệt thoáng khí, phù hợp chạy bộ nhẹ và đi hằng ngày.'],
    ['id' => 2,  'cat' => 'thoitrang', 'sub' => 'aosomi',        'tag' => 'Áo sơ mi',   'name' => 'Áo Sơ Mi Công Sở Nam Cao Cấp',          'price' => 450000,   'img' => site1_unsplash('photo-1596755094514-f87e34085b2c'), 'desc' => 'Áo sơ mi nam form vừa, chất vải mềm, ít nhăn, thích hợp đi làm và dự họp.'],
    ['id' => 3,  'cat' => 'thoitrang', 'sub' => 'aokhoac',       'tag' => 'Áo khoác',   'name' => 'Áo Khoác Gió Thể Thao Chống Nước',      'price' => 580000,   'img' => site1_unsplash('photo-1551028719-00167b16eac5'), 'desc' => 'Áo khoác gió nhẹ, chống thấm nước, có mũ trùm, gấp gọn dễ mang theo khi di chuyển.'],
    ['id' => 4,  'cat' => 'thoitrang', 'sub' => 'quanjeans',     'tag' => 'Quần jeans', 'name' => 'Quần Jeans Nam Dáng Slimfit Co Giãn',   'price' => 520000,   'img' => site1_unsplash('photo-1542272604-780c96856592'), 'desc' => 'Quần jeans slimfit chất denim co giãn, ôm vừa phải, thoải mái khi vận động.'],
    ['id' => 5,  'cat' => 'thoitrang', 'sub' => 'balo',          'tag' => 'Balo & Túi', 'name' => 'Balo Laptop Chống Nước 15.6 inch',      'price' => 680000,   'img' => site1_unsplash('photo-1553062407-98eeb64c6a62'), 'desc' => 'Balo có ngăn laptop đến 15.6 inch, vải chống nước, nhiều ngăn phụ cho phụ kiện.'],
    ['id' => 6,  'cat' => 'thoitrang', 'sub' => 'kinhmat',       'tag' => 'Kính mát',   'name' => 'Kính Mát Thời Trang Cao Cấp UV400',     'price' => 790000,   'img' => site1_unsplash('photo-1511499767150-a48a237f0083'), 'desc' => 'Kính mát tròng chống tia UV400, gọng nhẹ, phù hợp đi nắng và lái xe.'],
    ['id' => 7,  'cat' => 'thoitrang', 'sub' => 'munon',         'tag' => 'Mũ nón',     'name' => 'Mũ Lưỡi Trai Thể Thao Phong Cách',      'price' => 180000,   'img' => site1_unsplash('photo-1588850561407-ed78c282e89b'), 'desc' => 'Mũ lưỡi trai vải thoáng, quai chỉnh size phía sau, dễ phối với nhiều trang phục.'],
    ['id' => 8,  'cat' => 'thoitrang', 'sub' => 'phukiencanhan', 'tag' => 'Phụ kiện',   'name' => 'Bình Giữ Nhiệt Cao Cấp Inox 316',       'price' => 320000,   'img' => site1_unsplash('photo-1602143407151-7111542de6e8'), 'desc' => 'Bình giữ nhiệt inox 316 hai lớp, giữ nóng và giữ lạnh nhiều giờ, nắp kín không rò rỉ.'],
    // ===== THIẾT BỊ ĐIỆN TỬ =====
    ['id' => 9,  'cat' => 'dientu',    'sub' => 'laptop',        'tag' => 'Laptop',     'name' => 'Laptop Gaming Predator 16 Core i7',     'price' => 28990000, 'img' => site1_unsplash('photo-1603302576837-37561b2e2302'), 'desc' => 'Laptop gaming màn hình 16 inch, CPU Core i7, tản nhiệt kép, phù hợp chơi game và đồ họa.'],
    ['id' => 10, 'cat' => 'dientu',    'sub' => 'dienthoai',     'tag' => 'Điện thoại', 'name' => 'iPhone 18 Pro Max 256GB Chính Hãng',    'price' => 41490000, 'img' => site1_unsplash('photo-1695048133142-1a20484d2569'), 'desc' => 'Màn hình Super Retina XDR 6.9 inch, chip Apple A20 Pro 3nm, camera 48MP zoom 5x, pin dùng cả ngày.'],
    ['id' => 11, 'cat' => 'dientu',    'sub' => 'dongho',        'tag' => 'Đồng hồ thông minh', 'name' => 'Đồng Hồ Thông Minh Sport Watch', 'price' => 2490000,  'img' => site1_unsplash('photo-1523275335684-37898b6baf30'), 'desc' => 'Đồng hồ theo dõi nhịp tim, giấc ngủ và các bài tập thể thao, hiển thị thông báo từ điện thoại.'],
    ['id' => 12, 'cat' => 'dientu',    'sub' => 'amthanh',       'tag' => 'Âm thanh',   'name' => 'Tai Nghe Không Dây Bluetooth Chống Ồn', 'price' => 1150000,  'img' => site1_unsplash('photo-1505740420928-5e560c06d30e'), 'desc' => 'Tai nghe chụp tai Bluetooth, chống ồn chủ động, đàm thoại rõ, dùng liên tục nhiều giờ.'],
    ['id' => 13, 'cat' => 'dientu',    'sub' => 'amthanh',       'tag' => 'Âm thanh',   'name' => 'Loa Bluetooth Mini Bass Cực Đỉnh',      'price' => 750000,   'img' => site1_unsplash('photo-1608043152269-423dbba4e7e1'), 'desc' => 'Loa mini nhỏ gọn, âm bass dày, kháng nước nhẹ, kết nối Bluetooth ổn định.'],
    ['id' => 14, 'cat' => 'dientu',    'sub' => 'chuot_banphim', 'tag' => 'Chuột & Phím', 'name' => 'Bàn Phím Cơ Không Dây RGB',          'price' => 1450000,  'img' => site1_unsplash('photo-1587829741301-dc798b83add3'), 'desc' => 'Bàn phím cơ không dây, đèn nền RGB, switch êm, kết nối được nhiều thiết bị.'],
    ['id' => 15, 'cat' => 'dientu',    'sub' => 'chuot_banphim', 'tag' => 'Chuột & Phím', 'name' => 'Chuột Gaming Không Dây Siêu Nhẹ',    'price' => 890000,   'img' => site1_unsplash('photo-1615663245857-ac93bb7c39e7'), 'desc' => 'Chuột gaming không dây trọng lượng thấp, cảm biến chính xác, độ trễ thấp.'],
    ['id' => 16, 'cat' => 'dientu',    'sub' => 'sac_cucap',     'tag' => 'Sạc & Cáp',  'name' => 'Củ Sạc Nhanh GaN 65W 3 Cổng',           'price' => 450000,   'img' => site1_unsplash('photo-1583863788434-e58a36330cf0'), 'desc' => 'Củ sạc GaN 65W nhỏ gọn, 3 cổng sạc cùng lúc cho laptop, điện thoại và phụ kiện.'],
    ['id' => 17, 'cat' => 'dientu',    'sub' => 'camera',        'tag' => 'Nhiếp ảnh',  'name' => 'Máy Ảnh Mirrorless Quay 4K Siêu Nét',    'price' => 14200000, 'img' => site1_unsplash('photo-1516035069371-29a1b244cc32'), 'desc' => 'Máy ảnh mirrorless quay video 4K, lấy nét nhanh, ống kính rời, phù hợp chụp ảnh và làm nội dung.'],
];

// ---------------------------------------------------------------- HÀM DÙNG CHUNG

/** Bỏ dấu tiếng Việt + chữ thường, dùng cho tìm kiếm (không cần mbstring). */
function site1_normalize($s)
{
    static $map = null;
    if ($map === null) {
        $groups = array(
            'a' => 'àáạảãâầấậẩẫăằắặẳẵÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴ',
            'e' => 'èéẹẻẽêềếệểễÈÉẸẺẼÊỀẾỆỂỄ',
            'i' => 'ìíịỉĩÌÍỊỈĨ',
            'o' => 'òóọỏõôồốộổỗơờớợởỡÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠ',
            'u' => 'ùúụủũưừứựửữÙÚỤỦŨƯỪỨỰỬỮ',
            'y' => 'ỳýỵỷỹỲÝỴỶỸ',
            'd' => 'đĐ',
        );
        $map = array();
        foreach ($groups as $ascii => $chars) {
            foreach (preg_split('//u', $chars, -1, PREG_SPLIT_NO_EMPTY) as $ch) {
                $map[$ch] = $ascii;
            }
        }
    }
    $s = preg_replace('/[\x{0300}-\x{036f}]/u', '', (string)$s);   // dấu rời (kiểu gõ tổ hợp)
    return trim(strtolower(strtr($s, $map)));
}

/** Đọc & kiểm tra tham số URL: cat, sub, q, page. */
function site1_read_state(array $query)
{
    global $topCategories, $subCategories;

    $get = function ($key, $default = '') use ($query) {
        return (isset($query[$key]) && is_string($query[$key])) ? $query[$key] : $default;
    };

    $cat = $get('cat', DEFAULT_CATEGORY);
    if (!isset($topCategories[$cat])) {
        $cat = DEFAULT_CATEGORY;
    }

    $sub = $get('sub', 'all');
    if (!in_array($sub, array_column($subCategories[$cat], 'id'), true)) {
        $sub = 'all';
    }

    $kw   = trim($get('q'));
    if (preg_match('/^.{0,100}/us', $kw, $m)) {   // giới hạn 100 ký tự
        $kw = $m[0];
    }
    $page = max(1, (int)$get('page', '1'));

    return ['cat' => $cat, 'sub' => $sub, 'q' => $kw, 'page' => $page];
}

/** Tham số "sạch" (bỏ giá trị mặc định) để nối vào URL. */
function site1_state_params(array $s)
{
    return [
        'cat'  => $s['cat'],
        'sub'  => $s['sub'] !== 'all' ? $s['sub'] : null,
        'q'    => $s['q'] !== '' ? $s['q'] : null,
        'page' => $s['page'] > 1 ? $s['page'] : null,
    ];
}

function site1_url($file, array $params)
{
    $params = array_filter($params, function ($v) {
        return $v !== null && $v !== '' && $v !== false;
    });
    return $file . ($params ? '?' . http_build_query($params) : '');
}

/** Lọc: có từ khóa -> tìm trên toàn bộ sản phẩm; không có -> lọc theo danh mục. */
function site1_filter_products(array $products, $cat, $sub, $kw)
{
    if ($kw !== '') {
        $needle = site1_normalize($kw);
        return array_values(array_filter($products, function ($p) use ($needle) {
            return strpos(site1_normalize($p['name'] . ' ' . $p['tag']), $needle) !== false;
        }));
    }
    return array_values(array_filter($products, function ($p) use ($cat, $sub) {
        return $p['cat'] === $cat && ($sub === 'all' || $p['sub'] === $sub);
    }));
}

function site1_find_product($id)
{
    global $products;
    foreach ($products as $p) {
        if ((int)$p['id'] === (int)$id) {
            return $p;
        }
    }
    return null;
}

/** Phiên bản / kích cỡ / màu sắc hiển thị ở trang chi tiết. */
function site1_options(array $p)
{
    $price = $p['price'];
    $old   = (int)round($price * 1.05);
    $v = function ($name, $mul = 1.0) use ($price, $old) {
        return ['name' => $name, 'price' => (int)round($price * $mul), 'old' => (int)round($old * $mul)];
    };

    switch ($p['sub']) {
        case 'dienthoai':
            return [
                'label'    => 'Phiên bản khác:',
                'versions' => [
                    ['name' => '256GB', 'price' => 41490000, 'old' => 41999000],
                    ['name' => '512GB', 'price' => 47490000, 'old' => 48999000],
                    ['name' => '1TB',   'price' => 59990000, 'old' => 61999000],
                    ['name' => '2TB',   'price' => 80490000, 'old' => 82999000],
                ],
                'colors' => [
                    ['name' => 'Đỏ Burgundy', 'icon' => '🟤'],
                    ['name' => 'Băng thanh',  'icon' => '⚪'],
                    ['name' => 'Bạc',         'icon' => '🔘'],
                    ['name' => 'Đen',         'icon' => '⚫'],
                ],
            ];
        case 'laptop':
            return [
                'label'    => 'Cấu hình khác:',
                'versions' => [$v('Core i7 / 16GB / 512GB'), $v('Core i9 / 32GB / 1TB', 1.25), $v('Core i9 / 64GB / 2TB', 1.55)],
                'colors'   => [['name' => 'Đen Predator', 'icon' => '⚫'], ['name' => 'Xám Titan', 'icon' => '🔘']],
            ];
        case 'giay':
            return [
                'label'    => 'Kích cỡ (Size):',
                'versions' => [$v('Size 39'), $v('Size 40'), $v('Size 41'), $v('Size 42')],
                'colors'   => [['name' => 'Trắng Phối Đỏ', 'icon' => '⚪'], ['name' => 'Đen Classic', 'icon' => '⚫'], ['name' => 'Xám Thể Thao', 'icon' => '🔘']],
            ];
        case 'aosomi':
        case 'aokhoac':
            return [
                'label'    => 'Kích cỡ (Size):',
                'versions' => [$v('Size S (45-55kg)'), $v('Size M (56-65kg)'), $v('Size L (66-75kg)'), $v('Size XL (76-85kg)')],
                'colors'   => [['name' => 'Xanh Navy', 'icon' => '🔵'], ['name' => 'Trắng', 'icon' => '⚪'], ['name' => 'Đen Lịch Lãm', 'icon' => '⚫']],
            ];
        case 'quanjeans':
            return [
                'label'    => 'Kích cỡ (Size):',
                'versions' => [$v('Size 29'), $v('Size 30'), $v('Size 31'), $v('Size 32')],
                'colors'   => [['name' => 'Xanh Đậm', 'icon' => '🔵'], ['name' => 'Đen', 'icon' => '⚫'], ['name' => 'Xám Khói', 'icon' => '🔘']],
            ];
        default:
            return [
                'label'    => 'Phiên bản lựa chọn:',
                'versions' => [$v('Bản Tiêu Chuẩn'), $v('Bản Nâng Cấp Pro', 1.2)],
                'colors'   => [['name' => 'Màu Mặc Định', 'icon' => '📦'], ['name' => 'Màu Giới Hạn', 'icon' => '✨']],
            ];
    }
}