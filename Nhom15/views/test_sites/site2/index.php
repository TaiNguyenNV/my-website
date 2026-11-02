<?php
// views/test_sites/site2/index.php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cloud Services</title>
    <!-- Nhúng file CSS chung của dự án -->
    <link rel="stylesheet" href="../../../css/style1.css">
    <link rel="stylesheet" href="../../../css/site2.css">
</head>

<body>

    <!-- 1. Thanh Topbar hệ thống quản trị -->
    <div class="store-topbar">
        <span>⚡ Môi trường Landing Page Dịch vụ (Website ID = 2 - Kiểm tra phân tách dữ liệu)</span>
        <a href="../../dashboard.php">📊 Quay lại Dashboard Thống kê</a>
    </div>

    <!-- 2. Header dịch vụ nằm ngang trên cùng -->
    <header class="store-header">
        <div class="store-logo">
            <span>☁️</span> Cloud
        </div>
        <nav class="store-nav">
            <a href="#">Trang chủ</a>
            <a href="#">Dịch vụ Cloud</a>
            <a href="#">Bảng giá</a>
            <a href="#">Hỗ trợ kỹ thuật</a>
        </nav>
    </header>

    <!-- 3. Banner ngang toàn chiều rộng -->
    <div class="store-banner-horizontal">
        <h1>Hạ Tầng Cloud Hosting & VPS Tốc Độ Cao</h1>
        <p>Giải pháp lưu trữ đám mây tối ưu cho doanh nghiệp và nhà phát triển hiện đại.</p>
    </div>

    <!-- 4. Nội dung chính -->
    <div class="store-container">

        <!-- Thao tác TÌM KIẾM DỊCH VỤ (Search Tracker) -->
        <div class="ecommerce-search-box">
            <input type="text" id="txtSearch" class="form-control" placeholder="Tìm kiếm gói dịch vụ..."
                value="Gói Cloud Hosting">
            <button id="btnSearch" class="btn btn-primary" data-event="search"
                style="background: #7e22ce; border-color: #7e22ce;">🔍 Tìm kiếm</button>
        </div>

        <!-- Khu vực kết quả tìm kiếm -->
        <div id="searchResultBox">
            <div class="search-result-header">🔍 Kết quả tìm kiếm cho: "<span id="searchKeyword"></span>"</div>
            <div id="searchResultList"></div>
        </div>

        <!-- Danh sách gói dịch vụ nổi bật -->
        <div class="section-title">🚀 Các gói hạ tầng phổ biến</div>
        <div class="services-grid">
            <div class="service-card">
                <h3>Starter Cloud</h3>
                <div class="service-price">199,000 đ / tháng</div>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 15px;">Phù hợp cho website cá nhân, blog và
                    các dự án nhỏ mới khởi chạy.</p>
                <button class="btn btn-detail" data-event="view_detail" data-icon="☁️" data-name="Starter Cloud"
                    data-price="199,000 đ / tháng"
                    data-desc="Gói phù hợp cho website cá nhân và blog nhỏ. Tài nguyên: 1 vCPU, 1GB RAM, 20GB SSD, băng thông 100 Mbps. Uptime cam kết 99.9%. Hỗ trợ SSL miễn phí."
                    style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Đăng ký
                    ngay</button>
            </div>
            <div class="service-card" style="border: 2px solid #7e22ce;">
                <h3>Business Pro VPS</h3>
                <div class="service-price">499,000 đ / tháng</div>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 15px;">Dành cho doanh nghiệp vừa, cửa hàng
                    thương mại điện tử cần hiệu năng cao.</p>
                <button class="btn btn-detail" data-event="view_detail" data-icon="🖥️" data-name="Business Pro VPS"
                    data-price="499,000 đ / tháng"
                    data-desc="Giải pháp VPS cho doanh nghiệp vừa. Tài nguyên: 4 vCPU, 8GB RAM, 100GB NVMe SSD, 2TB băng thông/tháng. Hỗ trợ 24/7, snapshot hàng ngày, khôi phục 1 click."
                    style="width: 100%; background: #7e22ce; color: white;">Chọn gói này</button>
            </div>
            <div class="service-card">
                <h3>Enterprise Cloud</h3>
                <div class="service-price">1,299,000 đ / tháng</div>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 15px;">Hạ tầng độc lập, tài nguyên mạnh mẽ
                    phục vụ hệ thống quy mô lớn.</p>
                <button class="btn btn-detail" data-event="view_detail" data-icon="🏢" data-name="Enterprise Cloud"
                    data-price="1,299,000 đ / tháng"
                    data-desc="Hạ tầng riêng biệt cho hệ thống quy mô lớn. Tài nguyên: 16 vCPU, 32GB RAM, 500GB NVMe SSD, băng thông không giới hạn. SLA 99.99%, IP tĩnh, firewall riêng."
                    style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Liên hệ tư
                    vấn</button>
            </div>
        </div>

        <!-- Modal chi tiết dịch vụ -->
        <div id="productModal">
            <div class="modal-box">
                <button class="modal-close" id="modalClose">✕</button>
                <div class="modal-icon" id="modalIcon"></div>
                <div class="modal-name" id="modalName"></div>
                <div class="modal-price" id="modalPrice"></div>
                <div class="modal-desc" id="modalDesc"></div>
                <button class="btn" style="width:100%; background:#7e22ce; color:white; font-weight:600;">⭐ Đăng ký gói
                    này</button>
            </div>
        </div>

        <!-- 2 cột chức năng: Đăng ký tư vấn & Tải tài liệu kỹ thuật -->
        <div class="store-widgets-grid">
            <div class="store-card" style="margin-bottom: 0;">
                <h3 class="section-title" style="font-size: 1.1rem;">📝 Đăng ký tư vấn trực tuyến</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 16px;">
                    Kích hoạt chuỗi chuyển đổi (Funnel Step 2) dành riêng cho Site 2.
                </p>
                <button id="btnRegisterNow" class="btn" data-event="start"
                    style="width: 100%; background: #f59e0b; color: white;">
                    ⭐ Bắt đầu đăng ký dịch vụ ngay
                </button>
            </div>

            <div class="store-card" style="margin-bottom: 0;">
                <h3 class="section-title" style="font-size: 1.1rem;">📥 Tải tài liệu kỹ thuật</h3>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 16px;">
                    Tải về tài liệu hướng dẫn cấu hình API và Server bảo mật (PDF).
                </p>
                <button id="btnDownload" class="btn" data-event="download"
                    style="width: 100%; background: #3b82f6; color: white;">
                    📥 Tải whitepaper Cloud (PDF)
                </button>
            </div>
        </div>

        <!-- Thao tác GỬI BIỂU MẪU YÊU CẦU TƯ VẤN (Form Submit - Funnel Step 3) -->
        <div class="store-card">
            <h3 class="section-title" style="font-size: 1.1rem;">🚀 Gửi yêu cầu tư vấn (Submit)</h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">
                Thao tác hoàn tất chuỗi chuyển đổi (Funnel Step 3) trên Site 2.
            </p>
            <form id="contactForm" onsubmit="event.preventDefault(); alert('Đã gửi yêu cầu tư vấn thành công!');">
                <div style="margin-bottom: 15px;">
                    <label style="display:block; font-size: 0.88rem; font-weight: 600; margin-bottom: 6px;">Nhu cầu của
                        bạn:</label>
                    <input type="text" class="form-control" value="Cần tư vấn gói Doanh nghiệp">
                </div>
                <button type="submit" class="btn" style="background: #10b981; color: white;">
                    🚀 Gửi yêu cầu hoàn tất
                </button>
            </form>
        </div>

        <!-- Khung phản hồi trạng thái Tracker Site 2 -->
        <div>
            <h4 style="font-size: 0.95rem; color: #334155; margin-bottom: 8px;">📡 Nhật ký phản hồi từ Tracker (Site 2):
            </h4>
            <div id="liveTrackerLog" class="live-log-box">
                <div class="live-log-item" style="color: #c084fc;">
                    [Sẵn sàng] Tracker Site 2 đã kích hoạt. Nhấp chuột vào các nút phía trên để ghi nhận tương tác!
                </div>
            </div>
        </div>

    </div>

    <!-- Nhúng mã JavaScript Tracker cho Website ID = 2 -->
    <script src="../../../js/tracker.js"></script>
    <script>
        // ===== DỮ LIỆU DỊCH VỤ GIẢ (dùng cho Search & Detail) =====
        const SERVICES = [
            { icon: '☁️', name: 'Starter Cloud', price: '199,000 đ / tháng', desc: 'Gói phù hợp cho website cá nhân và blog nhỏ. Tài nguyên: 1 vCPU, 1GB RAM, 20GB SSD, băng thông 100 Mbps. Uptime cam kết 99.9%. Hỗ trợ SSL miễn phí.' },
            { icon: '🖥️', name: 'Business Pro VPS', price: '499,000 đ / tháng', desc: 'Giải pháp VPS cho doanh nghiệp vừa. Tài nguyên: 4 vCPU, 8GB RAM, 100GB NVMe SSD, 2TB băng thông/tháng. Hỗ trợ 24/7, snapshot hàng ngày, khôi phục 1 click.' },
            { icon: '🏢', name: 'Enterprise Cloud', price: '1,299,000 đ / tháng', desc: 'Hạ tầng riêng biệt cho hệ thống quy mô lớn. Tài nguyên: 16 vCPU, 32GB RAM, 500GB NVMe SSD, băng thông không giới hạn. SLA 99.99%, IP tĩnh, firewall riêng.' },
            { icon: '🔒', name: 'SSL & Security Pro', price: '99,000 đ / tháng', desc: 'Gói bảo mật toàn diện: chứng chỉ SSL Wildcard, WAF chống DDoS, quét malware tự động hàng ngày và báo cáo bảo mật theo thời gian thực.' },
            { icon: '📧', name: 'Business Email', price: '59,000 đ / tháng', desc: 'Email doanh nghiệp chuyên nghiệp với tên miền riêng. Dung lượng 25GB/tài khoản, chống spam, đồng bộ Outlook/Gmail, hỗ trợ IMAP/POP3/SMTP.' },
        ];

        // ===== MODAL CHI TIẾT DỊCH VỤ =====
        function openModal(service) {
            document.getElementById('modalIcon').textContent = service.icon;
            document.getElementById('modalName').textContent = service.name;
            document.getElementById('modalPrice').textContent = service.price;
            document.getElementById('modalDesc').textContent = service.desc;
            document.getElementById('productModal').classList.add('open');
        }

        document.getElementById('modalClose').addEventListener('click', function () {
            document.getElementById('productModal').classList.remove('open');
        });

        document.getElementById('productModal').addEventListener('click', function (e) {
            if (e.target === this) this.classList.remove('open');
        });

        // Gắn sự kiện cho các nút "Xem chi tiết / Đăng ký / Chọn gói"
        document.querySelectorAll('.btn-detail').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openModal({
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
                ? SERVICES.filter(s => s.name.toLowerCase().includes(kw))
                : SERVICES;

            if (matches.length === 0) {
                resultList.innerHTML = '<p style="color:#64748b;font-size:0.9rem;margin:8px 0 0 4px;">😕 Không tìm thấy dịch vụ phù hợp.</p>';
            } else {
                matches.forEach(function (s) {
                    const div = document.createElement('div');
                    div.className = 'search-result-item';
                    div.innerHTML = '<span class="sri-icon">' + s.icon + '</span>'
                        + '<div class="sri-info">'
                        + '<div class="sri-name">' + s.name + '</div>'
                        + '<div class="sri-price">' + s.price + '</div>'
                        + '</div>';
                    div.addEventListener('click', function () {
                        openModal(s);
                        window.WebTracker.trackEvent('view_detail', { element_text: s.name, element_tag: 'search-result' });
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
                websiteId: 2,
                apiEndpoint: '../../../handle/api_track.php'
            });

            window.addEventListener('tracker:event', function (e) {
                const detail = e.detail;
                const logBox = document.getElementById('liveTrackerLog');
                const time = new Date().toLocaleTimeString();

                const item = document.createElement('div');
                item.className = 'live-log-item';
                item.innerHTML = `<span style="color:#94a3b8;">[${time}]</span> 
                    <span style="color:#c084fc; font-weight:bold;">✔ [${detail.eventType.toUpperCase()}]</span> 
                    - Thao tác: <b>${detail.data.payload_data.element_text || detail.eventType}</b> 
                    <span style="color:#fbbf24;">(Status: ${detail.response.status})</span>`;

                logBox.prepend(item);
            });
        });
    </script>
</body>

</html>