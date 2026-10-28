# 🎓 KHOA CÔNG NGHỆ THÔNG TIN — ĐẠI HỌC ĐẠI NAM
## HỌC PHẦN: PHÁT TRIỂN PHẦN MỀM MÃ NGUỒN MỞ

---

# 📊 BÁO CÁO DỰ ÁN NHÓM 15
### **Đề tài 17: Công cụ thống kê lượt truy cập và phân tích thao tác website**

---

## 📖 1. Giới thiệu tổng quan

Dự án xây dựng hệ thống thu thập, tổng hợp số liệu truy cập và phân tích hành vi người dùng trên website theo thời gian thực. Hệ thống đáp ứng đầy đủ các tiêu chuẩn kỹ thuật khắt khe về **chống đếm trùng (Deduplication)**, **phát hiện xung đột dữ liệu (HTTP 409 Conflict)**, **phân tích chuỗi thao tác (Funnel Analysis)**, và **phân quyền bảo mật đa người dùng / đa website**.

### ✨ Các tính năng nổi bật:
- 🔄 **Chống đếm trùng lặp**: Nhận diện mã sự kiện duy nhất từ client (`event_id_unique`), gửi lại cùng payload không tăng đếm.
- ⚠️ **Phát hiện xung đột (HTTP 409 Conflict)**: Phát hiện và ghi log khi trùng mã sự kiện nhưng sai khác nội dung payload qua mã băm SHA-256.
- 🕒 **Chuẩn hóa múi giờ UTC+7**: Gom nhóm chính xác các sự kiện phát sinh quanh mốc nửa đêm (00:00:00).
- 📉 **Phân tích chuỗi hành vi (Funnel)**: Đo lường tỷ lệ chuyển đổi qua các bước: `Xem trang (Pageview)` ➔ `Bắt đầu (Start)` ➔ `Hoàn tất (Submit)`.
- 🔐 **Phân quyền truy cập 2 cấp**: 
  - Phân quyền theo vai trò hệ thống: `admin` vs `user`.
  - Phân quyền theo từng website (`allowed_users`).
- 🛡️ **Bảo mật & Quyền riêng tư**: Loại bỏ dữ liệu trường nhập nhạy cảm, mật khẩu và địa chỉ IP đầy đủ theo quy định an toàn thông tin.
- 📥 **Xuất báo cáo CSV**: Xuất dữ liệu báo cáo chuẩn UTF-8 BOM hiển thị tiếng Việt hoàn hảo trên Excel.

---

## 📁 2. Cấu trúc thư mục dự án

```text
Nhom15/
├── css/
│   ├── style.css             # Thiết kế giao diện chung & hệ thống màu sắc
│   ├── admin.css             # Giao diện Dashboard & biểu đồ Funnel
│   ├── login.css             # Giao diện Đăng nhập / Đăng ký
│   └── footer.css            # Giao diện thanh chân trang
├── database/
│   ├── db_connection.php     # Kết nối CSDL MySQL qua PDO
│   └── schema.sql            # Cấu trúc bảng & dữ liệu khởi tạo
├── functions/
│   ├── auth_functions.php    # Hàm kiểm tra đăng nhập & quyền xem website
│   ├── event_functions.php   # Xử lý sự kiện, chống đếm trùng & xung đột 409
│   ├── funnel_functions.php  # Thuật toán phân tích chuỗi chuyển đổi
│   └── report_functions.php  # Hàm tổng hợp báo cáo theo ngày
├── handle/
│   ├── api_track.php         # Endpoint tiếp nhận sự kiện JSON từ Tracker
│   ├── auth_process.php      # Xử lý xác thực đăng nhập
│   ├── register_process.php  # Xử lý đăng ký tài khoản (password_hash)
│   ├── export_report.php     # Xuất file báo cáo định dạng CSV
│   ├── website_process.php   # Xử lý thêm website theo dõi mới
│   └── logout.php            # Đăng xuất phiên làm việc
├── js/
│   └── tracker.js            # Thư viện JavaScript nhúng theo dõi phía client
├── tools/
│   ├── seed_data.php         # Script sinh tự động 200 sự kiện giả lập
│   └── run_tests.php         # Bộ kiểm thử tự động 5 tiêu chí Rubric
├── views/
│   ├── components/           # Thành phần giao diện tái sử dụng (header, footer)
│   ├── test_sites/           # Các website thử nghiệm để kiểm tra tracker
│   │   ├── site1/index.php   # Website thử nghiệm 1 (E-Commerce)
│   │   └── site2/index.php   # Website thử nghiệm 2 (Landing Page)
│   ├── dashboard.php         # Bảng điều khiển phân tích số liệu
│   ├── funnels.php           # Trang phân tích chuỗi thao tác
│   ├── websites.php          # Quản lý website & phân quyền thành viên
│   ├── login.php             # Trang đăng nhập hệ thống
│   └── register.php          # Trang đăng ký tài khoản
├── index.php                 # Điểm điều hướng trung tâm
└── README.md                 # Tài liệu hướng dẫn sử dụng dự án

⚙️ 3. Yêu cầu môi trườngHệ điều hành khuyến nghị: Ubuntu (WSL trên Windows hoặc Linux native).

PHP: Phiên bản >= 8.1 (kèm tiện ích mở rộng php-mysql, pdo_mysql).

Cơ sở dữ liệu: MySQL hoặc MariaDB Server.

Trình duyệt Web: Google Chrome, Microsoft Edge, Firefox.🚀 

4. Hướng dẫn cài đặt & Khởi chạy trên Ubuntu (WSL)

Bước 1: Khởi động MySQL và cấu hình CSDL Mở Terminal Ubuntu và chạy các lệnh sau:

Bash# 1. Khởi động dịch vụ MySQL

sudo service mysql start

# 2. Tạo Database 'nhom15' và cấp quyền cho tài khoản 'nhom15'

sudo mysql -e "CREATE DATABASE IF NOT EXISTS nhom15 CHARACTER SET utf8mb4 COLLATE 
utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'nhom15'@'localhost' IDENTIFIED BY '123456'; GRANT ALL PRIVILEGES ON nhom15.* TO 'nhom15'@'localhost'; CREATE USER IF NOT EXISTS 'nhom15'@'127.0.0.1' IDENTIFIED BY '123456'; GRANT ALL PRIVILEGES ON nhom15.* TO 'nhom15'@'127.0.0.1'; FLUSH PRIVILEGES;"

# 3. Nạp cấu trúc bảng và dữ liệu mẫu vào database (điều chỉnh lại đường dẫn cho khớp với máy của bạn)

sudo mysql nhom15 < database/schema.sql

Bước 2: Khởi chạy máy chủ Web PHPChuyển vào thư mục mã nguồn và khởi động server:Bashcd Nhom15

php -S 127.0.0.1:8000

(Giữ nguyên cửa sổ terminal này khi đang sử dụng hệ thống. Muốn dừng lại chỉ cần nhấn Ctrl + C).

🖥️ 5. Hướng dẫn sử dụng & Kịch bản kiểm thử

Mở trình duyệt web và truy cập địa chỉ: 👉 http://127.0.0.1:8000🔑 

5.1. Danh sách tài khoản thử nghiệm

Tài khoản   Mật khẩu    Quyền hệ thống  Website được phép xem

admin       123456      admin           Xem được tất cả website (Website 1, Website 2)

user1       123456      user            Chỉ được xem Website 1

Đăng ký mới (tự tạo)    user            Cần được admin cấp quyền trong danh sách

🧪 5.2. Kịch bản kiểm thử phân quyền (RBAC)

1.Đăng nhập admin:

    Truy cập vào Dashboard: Xem được báo cáo số liệu của cả Website 1 và Website 2.

    Có quyền truy cập menu Quản lý Website để đăng ký site mới.

2.Đăng xuất & Đăng nhập user1:

    Khi chọn Website 1: Xem báo cáo số liệu bình thường.

    Khi chuyển sang Website 2: Hệ thống lập tức chặn và hiển thị:

        ⛔ Truy cập bị từ chối: Tài khoản user1 không có quyền xem báo cáo của website ID 2.

🎲 5.3. Sinh dữ liệu giả lập (200 sự kiện)

Để có số liệu hiển thị trực quan trên biểu đồ và bảng báo cáo theo ngày:

Truy cập vào tab trình duyệt: 👉 http://127.0.0.1:8000/tools/seed_data.php

Hoặc chạy bằng dòng lệnh trong Ubuntu:

Bash

cd tools

php seed_data.php

🔬 5.4. Chạy bộ kiểm thử tự động (5 Phép thử Rubric)

Hệ thống tích hợp sẵn trang kiểm thử tự động đáp ứng trọn vẹn điểm đánh giá kiểm thử:

Truy cập vào đường dẫn: 👉 http://127.0.0.1:8000/tools/run_tests.php

Kết quả kiểm thử sẽ kiểm tra tự động:

✔ Phép thử 1: Đếm lượt xem, click và chống đếm trùng (Deduplication).

✔ Phép thử 2: Phát hiện xung đột dữ liệu trả về mã HTTP 409 Conflict.

✔ Phép thử 3: Kiểm tra xử lý múi giờ UTC+7 và mốc thời gian qua nửa đêm.

✔ Phép thử 4: Kiểm tra chuỗi thao tác Xem ➔ Bắt đầu ➔ Hoàn tất trong phiên.

✔ Phép thử 5: Kiểm tra phân tách dữ liệu đa website và bảo mật an toàn thông tin.

🌐 5.5. Kiểm tra mã Tracker trên website thử nghiệmMở trang thử nghiệm: 

👉 http://127.0.0.1:8000/views/test_sites/site1/index.php

Thao tác: Bấm nút tải tài liệu, gửi form thử nghiệm.

Kiểm tra Console trình duyệt (F12): Dữ liệu sự kiện được tự động đóng gói và gửi về api_track.

php mà không làm gián đoạn trải nghiệm người dùng.