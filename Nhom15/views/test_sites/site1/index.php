<?php
// views/test_sites/site1/index.php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store</title>
    <!-- Nhúng file CSS chung của dự án -->
    <link rel="stylesheet" href="../../../css/style1.css">
    <link rel="stylesheet" href="../../../css/site1.css">
</head>

<body>

    <!-- 1. Thanh Topbar hệ thống quản trị -->
    <div class="store-topbar">
        <span>⚡ Môi trường E-Commerce Mockup (Website ID = 1)</span>
        <a href="../../dashboard.php">📊 Quay lại Dashboard Thống kê</a>
    </div>

    <!-- 2. Header cửa hàng nằm ngang trên cùng -->
    <header class="store-header">
        <div class="store-logo">
            <span>🛍️</span> Store
        </div>
        <nav class="store-nav">
            <a href="#">Trang chủ</a>
            <a href="#">Sản phẩm</a>
            <a href="#">Khuyến mãi</a>
            <a href="#">Hỗ trợ</a>
        </nav>
    </header>

    <!-- 3. Banner ngang toàn chiều rộng nằm trên thanh tìm kiếm -->
    <div class="store-banner-horizontal">
        <h1>Bộ Sưu Tập Thời Trang & Thiết Bị 2026</h1>
        <p>Khám phá các sản phẩm chất lượng cao với dịch vụ chăm sóc khách hàng hàng đầu.</p>
    </div>

    <!-- 4. Nội dung chính chứa thanh tìm kiếm và các chức năng -->
    <div class="store-container">

        <!-- Thao tác TÌM KIẾM SẢN PHẨM (Search Tracker) -->
        <div class="ecommerce-search-box">
            <input type="text" id="txtSearch" class="form-control" placeholder="Nhập tên sản phẩm bạn muốn tìm..."
                value="Giày thể thao Nam">
            <button id="btnSearch" class="btn btn-primary" data-event="search">🔍 Tìm kiếm</button>
        </div>

        <!-- Khu vực kết quả tìm kiếm (hiện sau khi nhấn Tìm kiếm) -->
        <div id="searchResultBox">
            <div class="search-result-header">🔍 Kết quả tìm kiếm cho: "<span id="searchKeyword"></span>"</div>
            <div id="searchResultList"></div>
        </div>

        <!-- Danh sách sản phẩm trưng bày -->
        <div class="section-title">🔥 Sản phẩm nổi bật</div>
        <div class="products-grid">
            <div class="product-card">
                <div class="product-img">👟</div>
                <div class="product-body">
                    <div class="product-title">Giày Thể Thao Nam Nike Air</div>
                    <div class="product-price">1,250,000 đ</div>
                    <button class="btn btn-detail" data-event="view_detail" data-icon="👟"
                        data-name="Giày Thể Thao Nam Nike Air" data-price="1,250,000 đ"
                        data-desc="Giày thể thao nam cao cấp với công nghệ Nike Air Max đệm khí tiên tiến. Thiết kế hiện đại, phù hợp chạy bộ và thể thao hàng ngày. Có sẵn từ size 38–45."
                        style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Xem chi
                        tiết</button>
                </div>
            </div>
            <div class="product-card">
                <div class="product-img">👕</div>
                <div class="product-body">
                    <div class="product-title">Áo Sơ Mi Công Sở Nam Cao Cấp</div>
                    <div class="product-price">450,000 đ</div>
                    <button class="btn btn-detail" data-event="view_detail" data-icon="👕"
                        data-name="Áo Sơ Mi Công Sở Nam Cao Cấp" data-price="450,000 đ"
                        data-desc="Áo sơ mi nam chất liệu cotton cao cấp, form regular fit thoáng mát. Phù hợp văn phòng và đi tiệc. Nhiều màu: trắng, xanh, xám."
                        style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Xem chi
                        tiết</button>
                </div>
            </div>
            <div class="product-card">
                <div class="product-img">🎒</div>
                <div class="product-body">
                    <div class="product-title">Balo Laptop Chống Nước</div>
                    <div class="product-price">680,000 đ</div>
                    <button class="btn btn-detail" data-event="view_detail" data-icon="🎒"
                        data-name="Balo Laptop Chống Nước" data-price="680,000 đ"
                        data-desc="Balo chống nước cao cấp, ngăn laptop 15.6 inch, nhiều ngăn tiện lợi. Chất liệu polyester bền bỉ, dây đeo êm vai. Phù hợp đi làm và đi học."
                        style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Xem chi
                        tiết</button>
                </div>
            </div>
        </div>

        <!-- Modal chi tiết sản phẩm -->
        <div id="productModal">
            <div class="modal-box">
                <button class="modal-close" id="modalClose">✕</button>
                <div class="modal-img" id="modalIcon"></div>
                <div class="modal-name" id="modalName"></div>
                <div class="modal-price" id="modalPrice"></div>
                <div class="modal-desc" id="modalDesc"></div>
                <button class="btn" style="width:100%; background:#4f46e5; color:white; font-weight:600;">🛒 Thêm vào
                    giỏ hàng</button>
            </div>
        </div>

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

        <!-- Thao tác GỬI BIỂU MẪU ĐẶT HÀNG / THANH TOÁN (Form Submit - Funnel Step 3) -->
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

        <!-- Khung phản hồi trạng thái Tracker theo thời gian thực -->
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

    </div>

    <!-- Nhúng mã JavaScript Tracker cho Website ID = 1 -->
    <script src="../../../js/tracker.js"></script>
    <script>
        // ===== DỮ LIỆU SẢN PHẨM GIẢ (dùng cho Search & Detail) =====
        const PRODUCTS = [
            { icon: '👟', name: 'Giày Thể Thao Nam Nike Air', price: '1,250,000 đ', desc: 'Giày thể thao nam cao cấp với công nghệ Nike Air Max đệm khí tiên tiến. Thiết kế hiện đại, phù hợp chạy bộ và thể thao hàng ngày. Có sẵn từ size 38–45.' },
            { icon: '👕', name: 'Áo Sơ Mi Công Sở Nam Cao Cấp', price: '450,000 đ', desc: 'Áo sơ mi nam chất liệu cotton cao cấp, form regular fit thoáng mát. Phù hợp văn phòng và đi tiệc. Nhiều màu: trắng, xanh, xám.' },
            { icon: '🎒', name: 'Balo Laptop Chống Nước', price: '680,000 đ', desc: 'Balo chống nước cao cấp, ngăn laptop 15.6 inch, nhiều ngăn tiện lợi. Chất liệu polyester bền bỉ, dây đeo êm vai. Phù hợp đi làm và đi học.' },
            { icon: '⌚', name: 'Đồng Hồ Thông Minh SmartWatch', price: '2,100,000 đ', desc: 'Smartwatch đa năng với màn hình AMOLED, theo dõi sức khoẻ 24/7, pin 7 ngày, kháng nước IP68.' },
            { icon: '🕶️', name: 'Kính Mắt Thể Thao UV400', price: '320,000 đ', desc: 'Kính chống tia UV400, gọng nhẹ, ôm mặt, phù hợp cho cả chạy bộ, đạp xe và leo núi.' },
        ];

        // ===== MODAL CHI TIẾT SẢN PHẨM =====
        function openProductModal(product) {
            document.getElementById('modalIcon').textContent = product.icon;
            document.getElementById('modalName').textContent = product.name;
            document.getElementById('modalPrice').textContent = product.price;
            document.getElementById('modalDesc').textContent = product.desc;
            document.getElementById('productModal').classList.add('open');
        }

        document.getElementById('modalClose').addEventListener('click', function () {
            document.getElementById('productModal').classList.remove('open');
        });

        document.getElementById('productModal').addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('open');
        });

        // Gắn sự kiện "Xem chi tiết" cho các nút sản phẩm trong grid
        document.querySelectorAll('.btn-detail').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openProductModal({
                    icon: btn.dataset.icon,
                    name: btn.dataset.name,
                    price: btn.dataset.price,
                    desc: btn.dataset.desc,
                });
            });
        });

        // ===== TÌM KIẾM =====
        function doSearch(keyword) {
            const kw = keyword.trim().toLowerCase();
            const resultBox = document.getElementById('searchResultBox');
            const resultList = document.getElementById('searchResultList');
            document.getElementById('searchKeyword').textContent = keyword.trim() || '(trống)';
            resultList.innerHTML = '';

            const matches = kw
                ? PRODUCTS.filter(p => p.name.toLowerCase().includes(kw))
                : PRODUCTS;

            if (matches.length === 0) {
                resultList.innerHTML = '<p style="color:#64748b;font-size:0.9rem;margin:8px 0 0 4px;">😕 Không tìm thấy sản phẩm phù hợp.</p>';
            } else {
                matches.forEach(function (p) {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.innerHTML = '<span class="sri-icon">' + p.icon + '</span>'
                        + '<div class="sri-info">'
                        + '<div class="sri-name">' + p.name + '</div>'
                        + '<div class="sri-price">' + p.price + '</div>'
                        + '</div>';
                    div.addEventListener('click', function () {
                        openProductModal(p);
                        // Track thêm event view_detail khi click từ kết quả tìm kiếm
                        window.WebTracker.trackEvent('view_detail', { element_text: p.name, element_tag: 'search-result' });
                    });
                    resultList.appendChild(div);
                });
            }

            resultBox.style.display = 'block';
            resultBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        document.getElementById('btnSearch').addEventListener('click', function () {
            doSearch(document.getElementById('txtSearch').value);
        });

        document.getElementById('txtSearch').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') doSearch(this.value);
        });

        // ===== TRACKER INIT =====
        document.addEventListener("DOMContentLoaded", function () {
            window.WebTracker.init({
                websiteId: 1,
                apiEndpoint: '../../../handle/api_track.php'
            });

            window.addEventListener('tracker:event', function (e) {
                const detail = e.detail;
                const logBox = document.getElementById('liveTrackerLog');
                const time = new Date().toLocaleTimeString();

                const item = document.createElement('div');
                item.className = 'live-log-item';
                item.innerHTML = `<span style="color:#94a3b8;">[${time}]</span> 
                    <span style="color:#4ade80; font-weight:bold;">✔ [${detail.eventType.toUpperCase()}]</span> 
                    - Thao tác: <b>${detail.data.payload_data.element_text || detail.eventType}</b> 
                    <span style="color:#fbbf24;">(Status: ${detail.response.status})</span>`;

                logBox.prepend(item);
            });
        });
    </script>
</body>

</html>