<?php
// views/test_sites/site1/index.php
require __DIR__ . '/includes/products.php';
$isAdmin = site1_is_admin();   // chỉ admin thấy Live Activity Log

// ---- 1. Đọc bộ lọc từ URL: ?cat=&sub=&q=&page=
$state = site1_read_state($_GET);
$cat   = $state['cat'];
$sub   = $state['sub'];
$kw    = $state['q'];

// ---- 2. Lọc + phân trang
$filtered     = site1_filter_products($products, $cat, $sub, $kw);
$total        = count($filtered);
$totalPages   = max(1, (int)ceil($total / PRODUCTS_PER_PAGE));
$page         = min($state['page'], $totalPages);
$state['page'] = $page;
$pageProducts = array_slice($filtered, ($page - 1) * PRODUCTS_PER_PAGE, PRODUCTS_PER_PAGE);

$stateParams = site1_state_params($state);   // truyền sang product.php để nút "Quay lại" về đúng trang
$baseQuery   = [                              // dùng cho các nút phân trang
    'cat' => $cat,
    'sub' => $sub !== 'all' ? $sub : null,
    'q'   => $kw !== '' ? $kw : null,
];

// ---- 3. Tiêu đề khu vực sản phẩm
if ($kw !== '') {
    $sectionTitle = ($total > 0 ? '🔍 Kết quả tìm kiếm cho: "' : '🔍 Không có kết quả cho: "') . $kw . '"';
} else {
    $sectionTitle = '🔥 ' . $topCategories[$cat]['title'];
}
$countText = $total . ' sản phẩm' . ($totalPages > 1 ? ' · Trang ' . $page . '/' . $totalPages : '');

// ---- 4. Sự kiện Tracker gửi khi trang tải xong (chỉ ở trang 1 để không đếm lặp khi bấm sang trang khác)
$trackEvent = null;
if ($page === 1) {
    if ($kw !== '') {
        $trackEvent = ['type' => 'search', 'data' => [
            'element_text' => 'Tìm kiếm sản phẩm: ' . $kw,
            'keyword'      => $kw,
            'results_found' => $total,
        ]];
    } elseif (isset($_GET['cat'])) {
        if ($sub !== 'all') {
            $subName = '';
            foreach ($subCategories[$cat] as $s) {
                if ($s['id'] === $sub) { $subName = $s['name']; }
            }
            $trackEvent = ['type' => 'search', 'data' => [
                'element_text' => 'Lọc nhóm con: ' . $subName,
                'category'     => $cat,
                'subcategory'  => $sub,
            ]];
        } else {
            $trackEvent = ['type' => 'search', 'data' => [
                'element_text' => 'Chọn danh mục: ' . $topCategories[$cat]['label'],
                'category_id'  => $cat,
            ]];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhom15 Store - Website Thử Nghiệm 1</title>
    <!-- CSS chung của dự án + CSS riêng của site1 -->
    <link rel="stylesheet" href="../../../css/style1.css">
    <link rel="stylesheet" href="assets/site1.css">
</head>

<body>

    <!-- 1. Thanh Topbar hệ thống quản trị -->
    <div class="store-topbar">
        <span>⚡ Môi trường E-Commerce Mockup (Website ID = 1)</span>
        <a href="../../dashboard.php">📊 Quay lại Dashboard Thống kê</a>
    </div>

    <!-- 2. Header cửa hàng -->
    <header class="store-header">
        <a class="store-logo" href="index.php">
            <span class="logo-mark">N</span> Nhom15 Store
        </a>
        <nav class="store-nav">
            <a class="active" href="index.php">Trang chủ</a>
            <a href="index.php">Sản phẩm</a>
            <a href="#">Khuyến mãi</a>
            <a href="#">Hỗ trợ</a>
        </nav>
    </header>

    <!-- 3. Banner -->
    <div class="store-banner-horizontal">
        <h1>Bộ Sưu Tập Thời Trang & Thiết Bị <span class="accent">2026</span></h1>
        <p>Khám phá các sản phẩm chất lượng cao với dịch vụ chăm sóc khách hàng hàng đầu.</p>
    </div>

    <!-- 4. Nội dung chính -->
    <div class="store-container">

        <!-- TÌM KIẾM SẢN PHẨM (gửi ?q=...) -->
        <form class="ecommerce-search-box" method="get" action="index.php">
            <input type="text" id="txtSearch" name="q" class="form-control" value="<?= h($kw) ?>"
                placeholder="Nhập tên sản phẩm bạn muốn tìm (ví dụ: Giày, Laptop, Áo, Điện thoại...)" autocomplete="off">
            <button type="submit" id="btnSearch" class="btn btn-primary">🔍 Tìm kiếm</button>
        </form>

        <!-- Danh mục 2 tầng -->
        <div class="cat-nav-container">
            <div class="cat-top-row" id="topCatRow">
                <?php foreach ($topCategories as $key => $c): ?>
                    <a class="cat-top-btn<?= $key === $cat ? ' active' : '' ?>"
                       href="<?= h(site1_url('index.php', ['cat' => $key])) ?>"><?= h($c['label']) ?></a>
                <?php endforeach; ?>
            </div>

            <div class="cat-sub-row" id="subCatRow">
                <?php foreach ($subCategories[$cat] as $s): ?>
                    <a class="cat-sub-btn<?= $s['id'] === $sub ? ' active' : '' ?>"
                       href="<?= h(site1_url('index.php', ['cat' => $cat, 'sub' => $s['id'] !== 'all' ? $s['id'] : null])) ?>">
                        <?php if ($s['icon'] !== ''): ?><span class="sub-icon"><?= $s['icon'] ?></span><?php endif; ?>
                        <?= h($s['name']) ?>
                    </a>
                <?php endforeach; ?>
                <button type="button" class="cat-arrow-btn" id="btnSubScroll" title="Cuộn tiếp">›</button>
            </div>
        </div>

        <!-- Tiêu đề + số lượng -->
        <div class="section-title">
            <span id="currentCategoryTitle"><?= h($sectionTitle) ?></span>
            <span id="productCountBadge" class="section-count"><?= h($countText) ?></span>
        </div>

        <!-- Lưới sản phẩm (chỉ sản phẩm của trang hiện tại) -->
        <div class="products-grid" id="productsGrid">
            <?php foreach ($pageProducts as $p):
                $detailUrl = site1_url('product.php', ['id' => $p['id']] + $stateParams);
            ?>
                <div class="product-card">
                    <a class="product-img" href="<?= h($detailUrl) ?>">
                        <img src="<?= h($p['img']) ?>" alt="<?= h($p['name']) ?>" loading="lazy"
                             onerror="this.onerror=null;this.src='<?= SITE1_IMG_FALLBACK ?>'">
                    </a>
                    <div class="product-body">
                        <span class="product-tag tag-<?= h($p['cat']) ?>"><?= h($p['tag']) ?></span>
                        <div class="product-title"><a href="<?= h($detailUrl) ?>"><?= h($p['name']) ?></a></div>
                        <div class="product-price"><?= number_format($p['price']) ?> đ</div>
                        <a class="btn btn-view-detail" href="<?= h($detailUrl) ?>">Xem chi tiết</a>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if ($total === 0): ?>
                <div class="no-products" id="noProductsFound">
                    <div class="icon">🔍❌</div>
                    <h3>Không tìm thấy sản phẩm nào!</h3>
                    <p>
                        Rất tiếc, không có sản phẩm nào khớp với từ khóa "<b><?= h($kw) ?></b>".<br>
                        Vui lòng thử lại với tên khác (ví dụ: Giày, Laptop, Áo, Điện thoại, Tai nghe,...).
                    </p>
                    <a href="index.php" class="btn btn-primary">🔄 Xem lại tất cả sản phẩm</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Nút phân trang -->
        <?php include __DIR__ . '/includes/pagination.php'; ?>

        <!-- 2 cột chức năng: Đăng ký thành viên & Tải Catalogue -->
        <div class="store-widgets-grid">
            <div class="store-card" style="margin-bottom: 0;">
                <h3 class="section-title" style="font-size: 1.1rem;">📝 Đăng ký thành viên VIP</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 16px;">
                    Nhận mã giảm giá độc quyền và kích hoạt chuỗi tính năng thành viên (Funnel Step 2).
                </p>
                <button id="btnRegisterNow" class="btn" data-event="start"
                    style="width: 100%; background: #f59e0b; color: white;">
                    ⭐ Đăng ký tài khoản VIP ngay
                </button>
            </div>

            <div class="store-card" style="margin-bottom: 0;">
                <h3 class="section-title" style="font-size: 1.1rem;">📥 Tải Catalogue 2026</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 16px;">
                    Tải về tài liệu thông tin chi tiết các dòng sản phẩm mới nhất dưới định dạng PDF.
                </p>
                <button id="btnDownload" class="btn" data-event="download"
                    style="width: 100%; background: #8b5cf6; color: white;">
                    📥 Tải Catalogue sản phẩm (PDF)
                </button>
            </div>
        </div>

        <!-- GỬI BIỂU MẪU ĐẶT HÀNG / THANH TOÁN (Funnel Step 3) -->
        <div class="store-card">
            <h3 class="section-title" style="font-size: 1.1rem;">🚀 Hoàn tất đơn hàng / Gửi thông tin (Checkout Submit)
            </h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
                Thao tác hoàn tất chuỗi chuyển đổi (Funnel Step 3). Mật khẩu và thông tin nhạy cảm được bảo mật tuyệt
                đối.
            </p>
            <form id="contactForm" onsubmit="event.preventDefault(); alert('Đặt hàng / Gửi thông tin thành công!');">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div>
                        <label style="display:block; font-size: 0.88rem; font-weight: 600; margin-bottom: 6px;">Họ tên
                            khách hàng:</label>
                        <input type="text" class="form-control" value="Nguyễn Văn A">
                    </div>
                    <div>
                        <label style="display:block; font-size: 0.88rem; font-weight: 600; margin-bottom: 6px;">Mật khẩu
                            tài khoản (Bảo mật):</label>
                        <input type="password" class="form-control" value="password123">
                    </div>
                </div>
                <button type="submit" class="btn" style="background: #10b981; color: white;">
                    🚀 Xác nhận đặt hàng (Submit)
                </button>
            </form>
        </div>

        <!-- Nhật ký phản hồi Tracker theo thời gian thực (CHỈ ADMIN) -->
        <?php if ($isAdmin): ?>
        <div>
            <h4 style="font-size: 0.95rem; color: #334155; margin-bottom: 8px;">📡 Nhật ký phản hồi từ Tracker (Live
                Activity Log):</h4>
            <div id="liveTrackerLog" class="live-log-box">
                <div class="live-log-item" style="color: #38bdf8;">
                    [Sẵn sàng] Giao diện cửa hàng đã tải xong. WebTracker đang hoạt động ngầm, hãy tương tác với các nút
                    ở trên!
                </div>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- JavaScript Tracker cho Website ID = 1 -->
    <script src="../../../js/tracker.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Khởi tạo hệ thống theo dõi hành vi
            window.WebTracker.init({
                websiteId: 1,
                apiEndpoint: '../../../handle/api_track.php'
            });

            // Hiển thị phản hồi Tracker lên khung Live Log (khung này chỉ có với admin)
            window.addEventListener('tracker:event', function (e) {
                const detail = e.detail;
                const logBox = document.getElementById('liveTrackerLog');
                if (!logBox) return;
                const time = new Date().toLocaleTimeString();
                const text = (detail.data && detail.data.payload_data && detail.data.payload_data.element_text) || detail.eventType;
                const status = detail.response ? detail.response.status : '';

                const item = document.createElement('div');
                item.className = 'live-log-item';
                item.innerHTML = `<span style="color:#94a3b8;">[${time}]</span>
                    <span style="color:#4ade80; font-weight:bold;">✔ [${detail.eventType.toUpperCase()}]</span>
                    - Thao tác: <b>${text}</b>
                    <span style="color:#fbbf24;">(Status: ${status})</span>`;

                logBox.prepend(item);
            });

            // Nút mũi tên cuộn hàng danh mục con
            const subRow = document.getElementById('subCatRow');
            document.getElementById('btnSubScroll').addEventListener('click', function () {
                subRow.scrollBy({ left: 160, behavior: 'smooth' });
            });

            // Ghi nhận tìm kiếm / lọc danh mục (trang vừa tải theo kết quả đó)
            const pageEvent = <?= json_encode($trackEvent, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
            if (pageEvent && window.WebTracker) {
                window.WebTracker.trackEvent(pageEvent.type, pageEvent.data);
            }
        });
    </script>
</body>

</html>