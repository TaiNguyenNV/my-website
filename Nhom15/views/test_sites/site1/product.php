<?php
// views/test_sites/site1/product.php?id=3
require __DIR__ . '/includes/products.php';
$isAdmin = site1_is_admin();   // chỉ admin thấy Live Activity Log

$state   = site1_read_state($_GET);
$product = site1_find_product(isset($_GET['id']) && is_scalar($_GET['id']) ? (int)$_GET['id'] : 0);

// Link quay lại đúng trang danh sách (giữ danh mục, từ khóa, số trang)
$backUrl = site1_url('index.php', site1_state_params($state));

// ---- Không tìm thấy sản phẩm -> 404
if (!$product) {
    http_response_code(404);
    ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Không tìm thấy sản phẩm - Nhom15 Store</title>
    <link rel="stylesheet" href="../../../css/style1.css">
    <link rel="stylesheet" href="assets/site1.css">
</head>
<body>
    <div class="store-container" style="margin-top: 40px;">
        <div class="no-products">
            <div class="icon">🔍❌</div>
            <h3>Không tìm thấy sản phẩm!</h3>
            <p>Sản phẩm bạn đang xem không tồn tại hoặc đã bị gỡ.</p>
            <a href="<?= h($backUrl) ?>" class="btn btn-primary">← Quay lại danh sách</a>
        </div>
    </div>
</body>
</html>
    <?php
    exit;
}

$opt         = site1_options($product);
$first       = $opt['versions'][0];
$installment = (int)round($first['price'] * 0.7 / 6);
$catTitle    = $topCategories[$product['cat']]['title'];
$vn          = function ($n) { return number_format($n, 0, ',', '.'); };
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($product['name']) ?> - Nhom15 Store</title>
    <link rel="stylesheet" href="../../../css/style1.css">
    <link rel="stylesheet" href="assets/site1.css">
</head>

<body>

    <div class="store-topbar">
        <span>⚡ Môi trường E-Commerce Mockup (Website ID = 1)</span>
        <a href="../../dashboard.php">📊 Quay lại Dashboard Thống kê</a>
    </div>

    <header class="store-header">
        <a class="store-logo" href="index.php">
            <span class="logo-mark">N</span> Nhom15 Store
        </a>
        <nav class="store-nav">
            <a href="index.php">Trang chủ</a>
            <a class="active" href="index.php">Sản phẩm</a>
            <a href="#">Khuyến mãi</a>
            <a href="#">Hỗ trợ</a>
        </nav>
    </header>

    <div class="store-container" style="margin-top: 24px;">

        <nav class="breadcrumb">
            <a href="index.php">Trang chủ</a> ›
            <a href="<?= h(site1_url('index.php', ['cat' => $product['cat']])) ?>"><?= h($catTitle) ?></a> ›
            <span><?= h($product['name']) ?></span>
        </nav>

        <a class="back-link" href="<?= h($backUrl) ?>">← Quay lại danh sách</a>

        <div class="pd-card">
            <div class="pd-layout">

                <!-- CỘT TRÁI: hình ảnh + bảo hành -->
                <div>
                    <div class="pd-image-showcase">
                        <img src="<?= h($product['img']) ?>" alt="<?= h($product['name']) ?>"
                             onerror="this.onerror=null;this.src='<?= SITE1_IMG_FALLBACK ?>'">
                        <div class="pd-warranty-badge">
                            <span>🛡️</span>
                            <span>Sản phẩm chính hãng bảo hành <b>24 tháng miễn phí</b>.</span>
                        </div>
                    </div>
                </div>

                <!-- CỘT PHẢI: thông tin, giá, phiên bản, màu sắc -->
                <div>
                    <span class="product-tag tag-<?= h($product['cat']) ?>"><?= h($product['tag']) ?></span>
                    <h1 class="pd-title" id="pdTitle"><?= h($product['name']) ?></h1>

                    <div class="pd-meta">
                        <span style="color: #f59e0b;">⭐⭐⭐⭐⭐</span>
                        <span>(0 đánh giá)</span>
                        <span style="margin-left: auto; color: #475569;">Giá tại: <b style="color: #1e293b;">Hà Nội</b></span>
                    </div>

                    <p class="pd-desc"><?= nl2br(h($product['desc'])) ?></p>

                    <div class="pd-price-card">
                        <div>
                            <div class="pd-old-price" id="pdOldPrice"><?= $vn($first['old']) ?> đ</div>
                            <div class="pd-current-price" id="pdCurrentPrice"><?= $vn($first['price']) ?> đ</div>
                        </div>
                        <div class="pd-installment">
                            <span class="badge">Hoặc trả góp 0%</span><br>
                            Chỉ từ <b style="color: #1e293b;" id="pdInstallment"><?= $vn($installment) ?> đ</b> x 6 tháng<br>
                            Trả góp 0% lãi, trả trước 30%
                        </div>
                    </div>

                    <div class="pd-block">
                        <div class="pd-label"><?= h($opt['label']) ?></div>
                        <div class="pd-version-grid" id="pdVersionGrid">
                            <?php foreach ($opt['versions'] as $i => $ver): ?>
                                <div class="opt-pill<?= $i === 0 ? ' active' : '' ?>"
                                     data-name="<?= h($ver['name']) ?>"
                                     data-price="<?= (int)$ver['price'] ?>"
                                     data-old="<?= (int)$ver['old'] ?>">
                                    <span class="opt-name"><?= h($ver['name']) ?></span>
                                    <span class="opt-price"><?= $vn($ver['price']) ?> đ</span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="pd-block" style="margin-bottom: 22px;">
                        <div class="pd-label">Màu sắc:</div>
                        <div class="pd-color-grid" id="pdColorGrid">
                            <?php foreach ($opt['colors'] as $i => $col): ?>
                                <div class="color-card<?= $i === 0 ? ' active' : '' ?>" data-name="<?= h($col['name']) ?>">
                                    <div class="color-icon"><?= $col['icon'] ?></div>
                                    <div>
                                        <div class="color-name"><?= h($col['name']) ?></div>
                                        <div class="color-price"><?= $vn($first['price']) ?> đ</div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="pd-actions">
                        <button id="btnBuyNow" class="btn btn-buy">🚀 MUA NGAY (Giao tận nơi hoặc nhận tại cửa hàng)</button>
                        <div class="pd-actions-row">
                            <button id="btnAddToCart" class="btn btn-cart">🛒 Thêm vào giỏ hàng</button>
                            <button id="btnInstallment" class="btn btn-installment">💳 Trả góp 0% qua thẻ</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nhật ký Tracker (CHỈ ADMIN) -->
        <?php if ($isAdmin): ?>
        <div>
            <h4 style="font-size: 0.95rem; color: #334155; margin-bottom: 8px;">📡 Nhật ký phản hồi từ Tracker (Live Activity Log):</h4>
            <div id="liveTrackerLog" class="live-log-box">
                <div class="live-log-item" style="color: #38bdf8;">
                    [Sẵn sàng] Trang chi tiết sản phẩm đã tải xong.
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script src="../../../js/tracker.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            window.WebTracker.init({
                websiteId: 1,
                apiEndpoint: '../../../handle/api_track.php'
            });

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

            const title = <?= json_encode($product['name'], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;
            const fmt = n => Number(n).toLocaleString('vi-VN') + ' đ';

            const elCurrent = document.getElementById('pdCurrentPrice');
            const elOld = document.getElementById('pdOldPrice');
            const elInstallment = document.getElementById('pdInstallment');
            const versionPills = document.querySelectorAll('#pdVersionGrid .opt-pill');
            const colorCards = document.querySelectorAll('#pdColorGrid .color-card');

            let selectedVersion = versionPills[0].dataset.name;
            let selectedColor = colorCards[0].dataset.name;

            // Chọn phiên bản -> cập nhật giá
            versionPills.forEach(pill => {
                pill.addEventListener('click', function () {
                    versionPills.forEach(p => p.classList.remove('active'));
                    this.classList.add('active');
                    selectedVersion = this.dataset.name;

                    const price = parseInt(this.dataset.price, 10);
                    elCurrent.textContent = fmt(price);
                    elOld.textContent = fmt(this.dataset.old);
                    elInstallment.textContent = fmt(Math.round(price * 0.7 / 6));
                    document.querySelectorAll('#pdColorGrid .color-price').forEach(el => el.textContent = fmt(price));
                });
            });

            // Chọn màu sắc
            colorCards.forEach(card => {
                card.addEventListener('click', function () {
                    colorCards.forEach(c => c.classList.remove('active'));
                    this.classList.add('active');
                    selectedColor = this.dataset.name;
                });
            });

            // Ghi nhận lượt xem chi tiết sản phẩm
            if (window.WebTracker) {
                window.WebTracker.trackEvent('start', {
                    element_text: 'Xem chi tiết: ' + title,
                    product_title: title,
                    product_price: elCurrent.textContent.trim()
                });
            }

            // MUA NGAY
            document.getElementById('btnBuyNow').addEventListener('click', function () {
                const price = elCurrent.textContent.trim();
                alert(`🎉 ĐẶT HÀNG THÀNH CÔNG!\n\nSản phẩm: ${title}\nPhiên bản: ${selectedVersion}\nMàu sắc: ${selectedColor}\nTổng tiền: ${price}\n\nĐơn hàng đang được chuẩn bị giao đến bạn!`);
                if (window.WebTracker) {
                    window.WebTracker.trackEvent('submit', {
                        element_text: 'Mua ngay: ' + title,
                        product: title, version: selectedVersion, color: selectedColor, price: price
                    });
                }
            });

            // THÊM VÀO GIỎ HÀNG
            document.getElementById('btnAddToCart').addEventListener('click', function () {
                alert(`🛒 Đã thêm "${title} (${selectedVersion} - ${selectedColor})" vào giỏ hàng!`);
                if (window.WebTracker) {
                    window.WebTracker.trackEvent('submit', {
                        element_text: 'Thêm vào giỏ hàng: ' + title,
                        product: title, version: selectedVersion, color: selectedColor
                    });
                }
            });

            // TRẢ GÓP
            document.getElementById('btnInstallment').addEventListener('click', function () {
                alert(`💳 Đã gửi yêu cầu đăng ký trả góp 0% cho "${title}". Chuyên viên tài chính sẽ liên hệ với bạn trong 5 phút!`);
                if (window.WebTracker) {
                    window.WebTracker.trackEvent('start', {
                        element_text: 'Đăng ký trả góp 0%: ' + title,
                        product: title
                    });
                }
            });
        });
    </script>
</body>

</html>