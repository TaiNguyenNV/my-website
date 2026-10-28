# 📘 TÀI LIỆU HƯỚNG DẪN VẬN HÀNH DỰ ÁN CHI TIẾT
## ĐỀ TÀI 17: CÔNG CỤ THỐNG KÊ LƯỢT TRUY CẬP VÀ PHÂN TÍCH THAO TÁC WEBSITE
**Học phần:** Phát triển phần mềm mã nguồn mở — Khoa CNTT, Đại học Đại Nam  
**Đơn vị thực hiện:** Nhóm 15  

---

## I. MỤC TIÊU VÀ CHUẨN BỊ MÔI TRƯỜNG

Tài liệu này được thiết kế theo dạng **cầm tay chỉ việc**, hướng dẫn chi tiết từng bước để bất kỳ thành viên nào cũng có thể tự chạy, vận hành và kiểm thử toàn bộ hệ thống từ đầu trên môi trường **Ubuntu (WSL)** mà không gặp lỗi.

### 1. Yêu cầu công cụ cần có
- **Hệ điều hành**: Terminal Ubuntu (WSL trên Windows) hoặc Linux.
- **PHP CLI**: Phiên bản PHP từ `8.1` trở lên (kèm tiện ích mở rộng kết nối MySQL `php-mysql`).
- **MySQL Server**: Cơ sở dữ liệu quan hệ MySQL hoặc MariaDB.
- **Trình duyệt**: Chrome, Edge hoặc Firefox.

### 2. Cài đặt công cụ ban đầu trên Ubuntu (Chỉ làm 1 lần duy nhất)
Mở cửa sổ Terminal Ubuntu và chạy lệnh sau để đảm bảo máy có đủ công cụ:

```bash
sudo apt update
sudo apt install -y php-cli php-mysql mysql-server git unzip
```

Kiểm tra lại phiên bản sau khi cài đặt:
```bash
php -v
mysql --version
```
- [ ] Kết quả mong đợi: PHP hiển thị phiên bản `>= 8.1.x`, MySQL hiển thị phiên bản `8.x` hoặc MariaDB.

---

## II. THIẾT LẬP CƠ SỞ DỮ LIỆU (DATABASE)

Dự án sử dụng cơ sở dữ liệu có tên là **`nhom15`** và kết nối thông qua tài khoản riêng **`nhom15` / `123456`** để tránh hoàn toàn các lỗi bảo mật của tài khoản `root` trên Ubuntu.

### Bước 1: Khởi động dịch vụ MySQL
Trên Ubuntu WSL, dịch vụ MySQL không tự động bật khi khởi động máy. Bạn cần bật bằng lệnh:

```bash
sudo service mysql start
```
*Kết quả:* Hiện dòng `* Starting MySQL database server mysqld ... [ OK ]`.

### Bước 2: Tạo Database và Phân quyền người dùng
Copy và dán toàn bộ dòng lệnh sau vào terminal rồi bấm **Enter**:

```bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS nhom15 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER IF NOT EXISTS 'nhom15'@'localhost' IDENTIFIED BY '123456'; GRANT ALL PRIVILEGES ON nhom15.* TO 'nhom15'@'localhost'; CREATE USER IF NOT EXISTS 'nhom15'@'127.0.0.1' IDENTIFIED BY '123456'; GRANT ALL PRIVILEGES ON nhom15.* TO 'nhom15'@'127.0.0.1'; FLUSH PRIVILEGES;"
```
*(Nếu hệ thống hỏi mật khẩu sudo của Ubuntu thì nhập mật khẩu máy của bạn).*

### Bước 3: Nạp cấu trúc bảng và dữ liệu mẫu
Chạy lệnh nạp file `schema.sql` vào cơ sở dữ liệu:

```bash
sudo mysql nhom15 < /mnt/d/6.KY1NAM3/PTPM/code/Nhom15/database/schema.sql
```

Kiểm tra kết quả bước II:
- [ ] Chạy lệnh `sudo mysql nhom15 -e "SHOW TABLES;"` thấy xuất hiện đủ 7 bảng: `admins`, `error_logs`, `event_definitions`, `events`, `sessions`, `users`, `websites`.

---

## III. KHỞI CHẠY MÁY CHỦ WEB PHP

### Bước 1: Di chuyển vào thư mục mã nguồn
> ⚠️ **Chú ý:** Trong Linux bắt buộc phải có từ khóa `cd` ở đầu để đổi thư mục:

```bash
cd /mnt/d/6.KY1NAM3/PTPM/code/Nhom15
```

Gõ lệnh `ls` để kiểm tra, bạn sẽ thấy danh sách tệp: `database`, `functions`, `handle`, `views`, `tools`, `index.php`,...

### Bước 2: Bật máy chủ phát triển (Built-in Server)
Chạy lệnh:

```bash
php -S 127.0.0.1:8000
```

Màn hình terminal sẽ xuất hiện thông báo:
```text
PHP 8.x.x Development Server (http://127.0.0.1:8000) started
```

> 📌 **Lưu ý sống còn:** 
> - **Giữ nguyên cửa sổ terminal này**, không được tắt trong suốt quá trình dùng web.
> - Khi muốn dừng máy chủ: Bấm tổ hợp phím **`Ctrl + C`**.

---

## IV. QUY TRÌNH THỰC HÀNH VÀ KIỂM THỬ TỪNG TÍNH NĂNG

Mở trình duyệt Web (Chrome / Edge) và thực hiện các nội dung sau:

### Bài 1: Sinh 200 sự kiện giả lập (Chuẩn bị số liệu mẫu)
Để hệ thống có dữ liệu hiển thị trên bảng thống kê và biểu đồ Funnel:

- **Cách 1 (Khuyên dùng)**: Mở tab trình duyệt gõ:  
  👉 **`http://127.0.0.1:8000/tools/seed_data.php`**  
  *Màn hình thông báo:* `Đã sinh thành công 200 sự kiện giả lập vào cơ sở dữ liệu!`
- **Cách 2**: Mở một tab terminal Ubuntu khác và chạy:
  ```bash
  cd /mnt/d/6.KY1NAM3/PTPM/code/Nhom15/tools
  php seed_data.php
  ```

---

### Bài 2: Kiểm thử Phân quyền người dùng (Role-Based Access Control)

Mở tab trình duyệt: 👉 **`http://127.0.0.1:8000`** (hệ thống tự chuyển vào `views/login.php`).

#### Kịch bản A: Đăng nhập với quyền Quản trị viên (Admin)
1. Nhập tên đăng nhập: `admin` | Mật khẩu: `123456` ➔ Bấm **Đăng Nhập**.
2. **Quan sát kết quả**:
   - Thanh Menu trên hiển thị: *Xin chào, **admin** (Quyền: **admin**)*.
   - Thử chọn đổi giữa **Website 1** và **Website 2** trên bộ lọc: Xem được báo cáo số liệu của cả 2 website.
   - Có quyền vào trang **Quản lý Website** (`websites.php`) để thêm website mới.

#### Kịch bản B: Đăng nhập với quyền Người dùng thường (User)
1. Bấm **Đăng xuất** ở menu góc trái.
2. Đăng nhập tài khoản: `user1` | Mật khẩu: `123456`.
3. **Quan sát kết quả**:
   - Thanh Menu trên hiển thị: *Xin chào, **user1** (Quyền: **user**)*.
   - Khi chọn **Website 1**: Xem báo cáo số liệu bình thường (vì `user1` có trong danh sách được cấp phép).
   - **Thử nghiệm mấu chốt**: Chuyển bộ lọc sang **Website 2** ➔ Màn hình lập tức bị chặn và thông báo:
     > ⛔ **Truy cập bị từ chối**: Tài khoản **user1** không có quyền xem báo cáo của website ID 2.

#### Kịch bản C: Đăng ký tài khoản người dùng mới
1. Bấm vào liên kết **Đăng ký ngay** tại trang đăng nhập (`views/register.php`).
2. Nhập thông tin tài khoản mới (mật khẩu tự động được mã hóa an toàn qua thuật toán `password_hash`).
3. Đăng nhập thành công với vai trò mặc định là `user`.

---

### Bài 3: Chạy bộ kiểm thử tự động 5 tiêu chí Rubric

Mở tab trình duyệt và truy cập:  
👉 **`http://127.0.0.1:8000/tools/run_tests.php`**

Hệ thống sẽ chạy kịch bản kiểm thử tự động và trả kết quả màu xanh (✔ ĐẠT):
1. **Phép thử 1 (Đếm & Chống đếm trùng)**: Gửi sự kiện lần 1 thành công (HTTP 201), gửi lại lần 2 cùng mã sự kiện được nhận diện `duplicated = true` và không tăng số đếm.
2. **Phép thử 2 (Phát hiện xung đột HTTP 409)**: Cùng mã sự kiện nhưng sai khác payload ➔ Trả mã HTTP 409 Conflict và ghi log vào bảng `error_logs`.
3. **Phép thử 3 (Múi giờ UTC+7 & Nửa đêm)**: Các sự kiện lúc nửa đêm (00:00:01) được gom nhóm chính xác theo ngày công bố `DATE(event_timestamp)`.
4. **Phép thử 4 (Chuỗi thao tác Funnel)**: Chuỗi `Xem trang ➔ Bắt đầu ➔ Hoàn tất` được lọc đúng theo từng phiên làm việc (Session).
5. **Phép thử 5 (Cô lập dữ liệu & Bảo mật)**: Phân tách dữ liệu độc lập giữa các website; payload không chứa dữ liệu ô nhập hay mật khẩu.

---

### Bài 4: Kiểm tra mã Tracker JavaScript trên Website thử nghiệm

1. Mở trang: 👉 **`http://127.0.0.1:8000/views/test_sites/site1/index.php`**
2. Nhấn phím `F12` trên bàn phím để mở thanh Công cụ nhà phát triển ➔ Chọn tab **Console**.
3. Quan sát: Ngay khi tải trang, Tracker đã tự động bắn sự kiện `pageview`:
   ```text
   Tracking Success: {status: 201, message: "Ghi nhận sự kiện thành công.", duplicated: false}
   ```
4. Bấm vào nút **📥 Bấm tải tài liệu hướng dẫn** hoặc nhập form rồi bấm **Submit**:
   - Tracker tự động bắt sự kiện và gửi ngầm về backend `api_track.php` mà không làm tải lại trang.

---

### Bài 5: Xuất báo cáo thống kê định dạng CSV
1. Tại trang **Dashboard** (`views/dashboard.php`), chọn khoảng ngày cần xuất báo cáo.
2. Bấm nút màu xanh **📥 Xuất CSV**.
3. Trình duyệt tải về tệp `baocaothongke_website_1.csv`:
   - Mở tệp bằng Excel: Hiển thị đầy đủ tiếng Việt có dấu chuẩn xác (nhờ cơ chế chèn UTF-8 BOM `\xEF\xBB\xBF`).

---

## V. BẢNG TRA CỨU LỖI THƯỜNG GẶP VÀ CÁCH XỬ LÝ

| Hiện tượng lỗi | Nguyên nhân | Cách khắc phục chính xác |
|---|---|---|
| **`-bash: ...: Is a directory`** | Gõ thẳng đường dẫn thư mục mà quên lệnh `cd` ở đầu. | Thêm `cd ` trước đường dẫn, ví dụ: `cd /mnt/d/6.KY1NAM3/PTPM/code/Nhom15` |
| **`Failed to listen on 127.0.0.1:8000 (reason: Address already in use)`** | Cổng 8000 đang có một tiến trình PHP chạy trước đó chưa tắt. | Chạy lệnh `pkill -f "php -S"` để tắt tiến trình cũ, hoặc đổi cổng sang `php -S 127.0.0.1:8080`. |
| **`SQLSTATE[HY000] [1698] Access denied for user...`** | Chưa tạo user `nhom15` hoặc chưa nạp quyền trong MySQL. | Chạy lại **Bước 2 ở Phần II** trong tài liệu này để tạo user và cấp quyền cho `nhom15`. |
| **`SQLSTATE[HY000] [2002] Connection refused`** | Dịch vụ MySQL trên Ubuntu chưa được bật. | Chạy lệnh: `sudo service mysql start` |
| **`No syntax errors detected`** | Lệnh kiểm tra cú pháp thành công bình thường. | Đây là thông báo thành công của lệnh `php -l`. |

---

*Tài liệu lưu hành nội bộ phục vụ bảo vệ bài tập lớn — Nhóm 15 © 2026.*
