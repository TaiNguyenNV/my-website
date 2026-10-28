<?php
// views/test_sites/site2/index.php
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhom15 Cloud Services - Website Thử Nghiệm 2</title>
    <!-- Nhúng file CSS chung của dự án -->
    <link rel="stylesheet" href="../../../css/style1.css">
    <style>
        /* CSS đặc thù cho giao diện Landing Page Dịch vụ (Site 2) */
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            color: #334155;
        }

        .store-topbar {
            background: #0f172a;
            color: #fff;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9rem;
        }

        .store-topbar a {
            color: #c084fc;
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
        }

        .store-logo {
            font-size: 1.4rem;
            font-weight: bold;
            color: #7e22ce;
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
            color: #7e22ce;
        }

        .store-banner-horizontal {
            background: linear-gradient(135deg, #7e22ce, #3b82f6);
            color: white;
            padding: 40px 20px;
            text-align: center;
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

        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }

        .service-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 24px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
        }

        .service-price {
            color: #7e22ce;
            font-weight: bold;
            font-size: 1.15rem;
            margin: 10px 0 15px 0;
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
            border-left: 4px solid #c084fc;
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
        <span>⚡ Môi trường Landing Page Dịch vụ (Website ID = 2 - Kiểm tra phân tách dữ liệu)</span>
        <a href="../../dashboard.php">📊 Quay lại Dashboard Thống kê</a>
    </div>

    <!-- 2. Header dịch vụ nằm ngang trên cùng -->
    <header class="store-header">
        <div class="store-logo">
            <span>☁️</span> Nhom15 Cloud
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

        <!-- Danh sách gói dịch vụ nổi bật -->
        <div class="section-title">🚀 Các gói hạ tầng phổ biến</div>
        <div class="services-grid">
            <div class="service-card">
                <h3>Starter Cloud</h3>
                <div class="service-price">199,000 đ / tháng</div>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 15px;">Phù hợp cho website cá nhân, blog và
                    các dự án nhỏ mới khởi chạy.</p>
                <button class="btn"
                    style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Đăng ký
                    ngay</button>
            </div>
            <div class="service-card" style="border: 2px solid #7e22ce;">
                <h3>Business Pro VPS</h3>
                <div class="service-price">499,000 đ / tháng</div>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 15px;">Dành cho doanh nghiệp vừa, cửa hàng
                    thương mại điện tử cần hiệu năng cao.</p>
                <button class="btn" style="width: 100%; background: #7e22ce; color: white;">Chọn gói này</button>
            </div>
            <div class="service-card">
                <h3>Enterprise Cloud</h3>
                <div class="service-price">1,299,000 đ / tháng</div>
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 15px;">Hạ tầng độc lập, tài nguyên mạnh mẽ
                    phục vụ hệ thống quy mô lớn.</p>
                <button class="btn"
                    style="width: 100%; background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">Liên hệ tư
                    vấn</button>
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