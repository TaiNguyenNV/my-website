<?php
// views/test_sites/site1/index.php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhom15 Store - Website Thử Nghiệm 1</title>
    <!-- Nhúng file CSS chung của dự án -->
    <link rel="stylesheet" href="../../../css/style1.css">
    <style>
        /* Các CSS đặc thù riêng cho giao diện E-commerce Store */
        .store-topbar {
            background: #0f172a;
            color: #fff;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
            width: 100%;
        }

        .store-topbar a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
        }

        .store-header {
            background: #ffffff;
            padding: 15px 40px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            width: 100%;
        }

        .store-logo {
            font-size: 1.4rem;
            font-weight: bold;
            color: #4f46e5;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .store-nav {
            display: flex;
            gap: 25px;
            font-weight: 500;
        }

        .store-nav a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }

        .store-nav a:hover {
            color: #4f46e5;
        }

        .store-banner-horizontal {
            background: linear-gradient(135deg, #4f46e5, #06b6d4);
            color: white;
            padding: 40px 20px;
            text-align: center;
            width: 100%;
            margin-bottom: 30px;
            box-shadow: inset 0 -4px 6px rgba(0, 0, 0, 0.05);
        }

        .store-banner-horizontal h1 {
            font-size: 2.2rem;
            margin: 0 0 10px 0;
        }

        .store-banner-horizontal p {
            font-size: 1.05rem;
            opacity: 0.95;
            max-width: 700px;
            margin: 0 auto;
        }

        .store-container {
            max-width: 1100px;
            margin: 0 auto 50px auto;
            padding: 0 20px;
            width: 100%;
        }

        .ecommerce-search-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            margin-bottom: 30px;
            display: flex;
            gap: 12px;
            align-items: center;
            border: 1px solid #e2e8f0;
        }

        .ecommerce-search-box input {
            flex: 1;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: bold;
            color: #1e293b;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .product-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .product-img {
            height: 160px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .product-body {
            padding: 16px;
        }

        .product-title {
            font-size: 1.05rem;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .product-price {
            color: #f59e0b;
            font-weight: bold;
            font-size: 1.1rem;
            margin-bottom: 12px;
        }

        .store-widgets-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        @media (max-width: 768px) {
            .store-widgets-grid {
                grid-template-columns: 1fr;
            }
        }

        .store-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            margin-bottom: 25px;
        }

        .live-log-box {
            background: #0f172a;
            color: #f8fafc;
            padding: 16px;
            border-radius: 8px;
            font-family: monospace;
            font-size: 0.85rem;
            max-height: 200px;
            overflow-y: auto;
            border-left: 4px solid #38bdf8;
        }

        .live-log-item {
            margin-bottom: 6px;
            padding-bottom: 6px;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.15);
        }
    </style>
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
            <span>🛍️</span> Nhom15 Store
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

        <!-- Danh sách sản phẩm trưng bày -->
        <div class="section-title">🔥 Sản phẩm nổi bật</div>
        <div class="products-grid">
            <div class="product-card">
                <div class="product-img">👟</div>
                <div class="product-body">
                    <div class="product-title">Giày Thể Thao Nam Nike Air</div>
                    <div class="product-price">1,250,000 đ</div>
                    <button class="btn"
                        style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Xem chi
                        tiết</button>
                </div>
            </div>
            <div class="product-card">
                <div class="product-img">👕</div>
                <div class="product-body">
                    <div class="product-title">Áo Sơ Mi Công Sở Nam Cao Cấp</div>
                    <div class="product-price">450,000 đ</div>
                    <button class="btn"
                        style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Xem chi
                        tiết</button>
                </div>
            </div>
            <div class="product-card">
                <div class="product-img">🎒</div>
                <div class="product-body">
                    <div class="product-title">Balo Laptop Chống Nước</div>
                    <div class="product-price">680,000 đ</div>
                    <button class="btn"
                        style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Xem chi
                        tiết</button>
                </div>
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