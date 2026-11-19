<?php
// views/test_sites/site1/includes/products.php
// Dữ liệu + hàm dùng chung cho index.php (danh sách) và product.php (chi tiết)

const PRODUCTS_PER_PAGE = 8;          // số sản phẩm mỗi trang (đổi tại đây)
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
    ['id' => 4,  'cat' => 'thoitrang', 'sub' => 'quanjeans',     'tag' => 'Quần jeans', 'name' => 'Quần Jeans Nam Dáng Slimfit Co Giãn',   'price' => 520000,   'img' => site1_unsplash('photo-1541099649105-f69ad21f3246'), 'desc' => 'Quần jeans slimfit chất denim co giãn, ôm vừa phải, thoải mái khi vận động.'],
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

    // ===== THỜI TRANG - BỔ SUNG =====
    ['id' => 18, 'cat' => 'thoitrang', 'sub' => 'giay',          'tag' => 'Giày dép',   'name' => 'Giày Sneaker Nữ Trắng Cổ Thấp',           'price' => 890000,   'img' => site1_unsplash('photo-1608231387042-66d1773070a5'), 'desc' => 'Sneaker nữ đế bệt, thiết kế tối giản, dễ phối đồ, đi êm chân cả ngày.'],
    ['id' => 19, 'cat' => 'thoitrang', 'sub' => 'giay',          'tag' => 'Giày dép',   'name' => 'Giày Chạy Bộ Adidas Ultraboost 24',        'price' => 2490000,  'img' => site1_unsplash('photo-1491553895911-0055eca6402d'), 'desc' => 'Giày chạy bộ Ultraboost đế Boost hoàn trả năng lượng, lưới Primeknit ôm chân.'],
    ['id' => 20, 'cat' => 'thoitrang', 'sub' => 'giay',          'tag' => 'Giày dép',   'name' => 'Dép Quai Ngang Thể Thao Đế Dày',           'price' => 350000,   'img' => site1_unsplash('photo-1603808033192-082d6919d3e1'), 'desc' => 'Dép quai ngang đế mềm, đệm êm, chống trượt, phù hợp đi biển và mặc nhà.'],
    ['id' => 21, 'cat' => 'thoitrang', 'sub' => 'aosomi',        'tag' => 'Áo sơ mi',   'name' => 'Áo Sơ Mi Nữ Oxford Dài Tay',               'price' => 380000,   'img' => site1_unsplash('photo-1598032895397-b9472444bf93'), 'desc' => 'Sơ mi nữ chất Oxford, cổ đức, dài tay, phong cách công sở thanh lịch.'],
    ['id' => 22, 'cat' => 'thoitrang', 'sub' => 'aosomi',        'tag' => 'Áo sơ mi',   'name' => 'Áo Sơ Mi Caro Flannel Unisex',              'price' => 420000,   'img' => site1_unsplash('photo-1589310243389-96a5483213a8'), 'desc' => 'Sơ mi flannel caro dày dặn, giữ ấm tốt, mặc khoác ngoài hoặc mặc đơn.'],
    ['id' => 23, 'cat' => 'thoitrang', 'sub' => 'aokhoac',       'tag' => 'Áo khoác',   'name' => 'Áo Khoác Da Thời Trang Unisex',             'price' => 1190000,  'img' => site1_unsplash('photo-1548126032-079a0fb0099d'), 'desc' => 'Áo khoác da PU lót nhung ấm, dáng biker cá tính, unisex nam nữ đều mặc được.'],
    ['id' => 24, 'cat' => 'thoitrang', 'sub' => 'aokhoac',       'tag' => 'Áo khoác',   'name' => 'Áo Hoodie Nỉ Bông Oversize',                'price' => 490000,   'img' => site1_unsplash('photo-1556821840-3a63f95609a7'), 'desc' => 'Hoodie nỉ bông dày, dáng oversize trẻ trung, có túi kangoo và mũ trùm.'],
    ['id' => 25, 'cat' => 'thoitrang', 'sub' => 'quanjeans',     'tag' => 'Quần jeans', 'name' => 'Quần Jeans Nữ Ống Rộng Retro',              'price' => 490000,   'img' => site1_unsplash('photo-1576995853123-5a10305d93c0'), 'desc' => 'Jeans nữ ống rộng suông, cạp cao tôn dáng, phong cách retro Y2K.'],
    ['id' => 26, 'cat' => 'thoitrang', 'sub' => 'quanjeans',     'tag' => 'Quần jeans', 'name' => 'Quần Short Jeans Nam Rách Gối',              'price' => 350000,   'img' => site1_unsplash('photo-1591195853828-11db59a44f6b'), 'desc' => 'Short jeans rách nhẹ, cá tính, chất denim mềm, thoải mái mùa hè.'],
    ['id' => 27, 'cat' => 'thoitrang', 'sub' => 'balo',          'tag' => 'Balo & Túi', 'name' => 'Túi Xách Nữ Đeo Vai Da PU',                'price' => 420000,   'img' => site1_unsplash('photo-1548036328-c9fa89d128fa'), 'desc' => 'Túi xách nữ da PU mềm, nhiều ngăn, thiết kế thanh lịch đi làm hoặc đi chơi.'],
    ['id' => 28, 'cat' => 'thoitrang', 'sub' => 'balo',          'tag' => 'Balo & Túi', 'name' => 'Balo Mini Nữ Da Mềm Phong Cách Hàn',       'price' => 390000,   'img' => site1_unsplash('photo-1581605405669-fcdf81165afa'), 'desc' => 'Balo mini da mềm, gọn nhẹ, phong cách Hàn Quốc, đựng vừa đồ thiết yếu.'],
    ['id' => 29, 'cat' => 'thoitrang', 'sub' => 'kinhmat',       'tag' => 'Kính mát',   'name' => 'Kính Mát Gọng Kim Loại Pilot Cổ Điển',     'price' => 650000,   'img' => site1_unsplash('photo-1572635196237-14b3f281503f'), 'desc' => 'Kính pilot gọng kim loại mảnh, tròng phân cực, chống chói khi lái xe.'],
    ['id' => 30, 'cat' => 'thoitrang', 'sub' => 'munon',         'tag' => 'Mũ nón',     'name' => 'Nón Bucket Unisex Vải Nỉ Mùa Đông',        'price' => 220000,   'img' => site1_unsplash('photo-1534215754734-18e55d13e346'), 'desc' => 'Nón bucket vải nỉ dày, giữ ấm tốt, phong cách street style.'],
    ['id' => 31, 'cat' => 'thoitrang', 'sub' => 'munon',         'tag' => 'Mũ nón',     'name' => 'Mũ Snapback Thêu Logo Hip Hop',             'price' => 250000,   'img' => site1_unsplash('photo-1576871337632-b9aef4c17ab9'), 'desc' => 'Mũ snapback thêu logo, chỉnh size phía sau, phong cách hip-hop, streetwear.'],
    ['id' => 32, 'cat' => 'thoitrang', 'sub' => 'phukiencanhan', 'tag' => 'Phụ kiện',   'name' => 'Đồng Hồ Dây Da Nam Cổ Điển',               'price' => 950000,   'img' => site1_unsplash('photo-1524592094714-0f0654e20314'), 'desc' => 'Đồng hồ kim dây da bò thật, mặt kính chống xước, thiết kế thanh lịch cổ điển.'],
    ['id' => 33, 'cat' => 'thoitrang', 'sub' => 'phukiencanhan', 'tag' => 'Phụ kiện',   'name' => 'Ví Da Bò Thật Nam Dáng Ngang',              'price' => 450000,   'img' => site1_unsplash('photo-1627123424574-724758594e93'), 'desc' => 'Ví da bò nguyên miếng, nhiều ngăn thẻ, thiết kế mỏng gọn bỏ túi quần.'],
    ['id' => 34, 'cat' => 'thoitrang', 'sub' => 'phukiencanhan', 'tag' => 'Phụ kiện',   'name' => 'Thắt Lưng Da Bò Khóa Tự Động',             'price' => 380000,   'img' => site1_unsplash('photo-1608461864721-b8f50c91c147'), 'desc' => 'Thắt lưng da bò khóa tự động inox, cắt size tùy chỉnh, bền đẹp lâu dài.'],

    // ===== THIẾT BỊ ĐIỆN TỬ - BỔ SUNG =====
    ['id' => 35, 'cat' => 'dientu',    'sub' => 'laptop',        'tag' => 'Laptop',     'name' => 'MacBook Air M3 13 inch Midnight',           'price' => 31990000, 'img' => site1_unsplash('photo-1496181133206-80ce9b88a853'), 'desc' => 'MacBook Air chip M3, màn Liquid Retina, pin 18 giờ, thiết kế mỏng nhẹ.'],
    ['id' => 36, 'cat' => 'dientu',    'sub' => 'laptop',        'tag' => 'Laptop',     'name' => 'Dell XPS 15 OLED Core i9 RTX 4070',        'price' => 42990000, 'img' => site1_unsplash('photo-1525547719571-a2d4ac8945e2'), 'desc' => 'Laptop Dell XPS 15 màn OLED 3.5K, Core i9, RTX 4070, dành cho sáng tạo nội dung.'],
    ['id' => 37, 'cat' => 'dientu',    'sub' => 'laptop',        'tag' => 'Laptop',     'name' => 'Laptop Văn Phòng HP Pavilion 14 Ryzen 5',   'price' => 14990000, 'img' => site1_unsplash('photo-1484788984921-03950022c9ef'), 'desc' => 'Laptop HP Pavilion 14 inch, Ryzen 5, 8GB RAM, SSD 512GB, nhẹ chỉ 1.4kg.'],
    ['id' => 38, 'cat' => 'dientu',    'sub' => 'dienthoai',     'tag' => 'Điện thoại', 'name' => 'Samsung Galaxy S25 Ultra 256GB',             'price' => 26990000, 'img' => site1_unsplash('photo-1598327105666-5b89351aff97'), 'desc' => 'Galaxy S25 Ultra bút S-Pen, camera 200MP AI, Snapdragon Gen 4, pin 5000mAh.'],
    ['id' => 39, 'cat' => 'dientu',    'sub' => 'dienthoai',     'tag' => 'Điện thoại', 'name' => 'Google Pixel 9 Pro 256GB',                  'price' => 22490000, 'img' => site1_unsplash('photo-1511707171634-5f897ff02aa9'), 'desc' => 'Pixel 9 Pro camera AI hàng đầu, Android gốc mượt mà, Tensor G4.'],
    ['id' => 40, 'cat' => 'dientu',    'sub' => 'dienthoai',     'tag' => 'Điện thoại', 'name' => 'Xiaomi 15 Ultra 5G 512GB',                  'price' => 18990000, 'img' => site1_unsplash('photo-1580910051074-3eb694886505'), 'desc' => 'Xiaomi 15 Ultra camera Leica, Snapdragon 8 Gen 4, sạc 120W siêu nhanh.'],
    ['id' => 41, 'cat' => 'dientu',    'sub' => 'dongho',        'tag' => 'Đồng hồ thông minh', 'name' => 'Apple Watch Series 10 45mm GPS', 'price' => 10990000, 'img' => site1_unsplash('photo-1579586337278-3befd40fd17a'), 'desc' => 'Apple Watch S10 màn OLED lớn hơn, đo SpO2, ECG, phát hiện ngã, chống nước 50m.'],
    ['id' => 42, 'cat' => 'dientu',    'sub' => 'dongho',        'tag' => 'Đồng hồ thông minh', 'name' => 'Samsung Galaxy Watch 7 Classic', 'price' => 7990000,  'img' => site1_unsplash('photo-1508685096489-7aacd43bd3b1'), 'desc' => 'Galaxy Watch 7 Classic viền xoay, đo huyết áp, Wear OS, pin 2 ngày.'],
    ['id' => 43, 'cat' => 'dientu',    'sub' => 'amthanh',       'tag' => 'Âm thanh',   'name' => 'AirPods Pro 2 USB-C Chính Hãng',            'price' => 5990000,  'img' => site1_unsplash('photo-1590658268037-6bf12165a8df'), 'desc' => 'AirPods Pro 2 chống ồn chủ động, âm thanh không gian, cổng USB-C.'],
    ['id' => 44, 'cat' => 'dientu',    'sub' => 'amthanh',       'tag' => 'Âm thanh',   'name' => 'Loa Bluetooth JBL Charge 5 Chống Nước',     'price' => 3190000,  'img' => site1_unsplash('photo-1608043152269-423dbba4e7e1'), 'desc' => 'JBL Charge 5 bass mạnh, chống nước IP67, pin 20 giờ, sạc được cho điện thoại.'],
    ['id' => 45, 'cat' => 'dientu',    'sub' => 'amthanh',       'tag' => 'Âm thanh',   'name' => 'Tai Nghe Gaming Razer BlackShark V2',       'price' => 2290000,  'img' => site1_unsplash('photo-1618366712010-f4ae9c647dcb'), 'desc' => 'Tai nghe gaming có mic, driver 50mm, THX Spatial Audio, nhẹ và thoải mái.'],
    ['id' => 46, 'cat' => 'dientu',    'sub' => 'chuot_banphim', 'tag' => 'Chuột & Phím', 'name' => 'Chuột Văn Phòng Logitech M590 Silent', 'price' => 590000,   'img' => site1_unsplash('photo-1527864550417-7fd91fc51a46'), 'desc' => 'Chuột không dây siêu êm, kết nối 2 máy tính, pin AA dùng 24 tháng.'],
    ['id' => 47, 'cat' => 'dientu',    'sub' => 'sac_cucap',     'tag' => 'Sạc & Cáp',  'name' => 'Pin Dự Phòng 20000mAh Anker PowerCore',    'price' => 790000,   'img' => site1_unsplash('photo-1609091839311-d5365f9ff1c5'), 'desc' => 'Pin dự phòng Anker 20000mAh, sạc nhanh 22.5W, 2 cổng USB-C, nhỏ gọn.'],
    ['id' => 48, 'cat' => 'dientu',    'sub' => 'sac_cucap',     'tag' => 'Sạc & Cáp',  'name' => 'Cáp Sạc Nhanh USB-C to C 100W 2m',         'price' => 180000,   'img' => site1_unsplash('photo-1587037542794-6ca5f4772330'), 'desc' => 'Cáp USB-C 100W dài 2m, hỗ trợ sạc nhanh PD cho laptop và điện thoại.'],
    ['id' => 49, 'cat' => 'dientu',    'sub' => 'camera',        'tag' => 'Nhiếp ảnh',  'name' => 'GoPro HERO 13 Black Action Camera',         'price' => 11990000, 'img' => site1_unsplash('photo-1561154464-82e9adf32764'), 'desc' => 'Camera hành trình GoPro HERO 13, quay 5.3K, chống nước 10m, ổn định HyperSmooth 7.'],
    ['id' => 50, 'cat' => 'dientu',    'sub' => 'camera',        'tag' => 'Nhiếp ảnh',  'name' => 'Máy Ảnh Canon EOS R50 Kit 18-45mm',         'price' => 19990000, 'img' => site1_unsplash('photo-1502920917128-1aa500764cbd'), 'desc' => 'Canon EOS R50 mirrorless nhẹ, quay 4K, AF mắt nhanh, kèm lens kit 18-45mm.'],
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
        case 'dongho':
            return [
                'label'    => 'Phiên bản:',
                'versions' => [$v('41mm GPS'), $v('45mm GPS', 1.08), $v('45mm GPS + Cellular', 1.25)],
                'colors'   => [['name' => 'Đen Midnight', 'icon' => '⚫'], ['name' => 'Bạc Starlight', 'icon' => '⚪'], ['name' => 'Xanh Storm', 'icon' => '🔵']],
            ];
        case 'amthanh':
            return [
                'label'    => 'Phiên bản:',
                'versions' => [$v('Bản Tiêu Chuẩn'), $v('Bản Nâng Cấp Hi-Res', 1.15)],
                'colors'   => [['name' => 'Đen', 'icon' => '⚫'], ['name' => 'Trắng', 'icon' => '⚪'], ['name' => 'Xanh Navy', 'icon' => '🔵']],
            ];
        case 'sac_cucap':
            return [
                'label'    => 'Phiên bản:',
                'versions' => [$v('Bản 1 cổng'), $v('Bản 3 cổng', 1.3)],
                'colors'   => [['name' => 'Trắng', 'icon' => '⚪'], ['name' => 'Đen', 'icon' => '⚫']],
            ];
        case 'camera':
            return [
                'label'    => 'Gói lựa chọn:',
                'versions' => [$v('Body Only'), $v('Kit (kèm lens)', 1.2)],
                'colors'   => [['name' => 'Đen', 'icon' => '⚫'], ['name' => 'Bạc', 'icon' => '🔘']],
            ];
        default:
            return [
                'label'    => 'Phiên bản lựa chọn:',
                'versions' => [$v('Bản Tiêu Chuẩn'), $v('Bản Nâng Cấp Pro', 1.2)],
                'colors'   => [['name' => 'Màu Mặc Định', 'icon' => '📦'], ['name' => 'Màu Giới Hạn', 'icon' => '✨']],
            ];
    }
}