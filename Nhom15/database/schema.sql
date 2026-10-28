-- Cấu trúc cơ sở dữ liệu hoàn chỉnh cho Nhom15: Thống kê lượt truy cập và thao tác website

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS events;
DROP TABLE IF EXISTS error_logs;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS event_definitions;
DROP TABLE IF EXISTS websites;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS admins;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Bảng quản trị viên (Admin)
CREATE TABLE admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng người dùng hệ thống (Users)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    role VARCHAR(20) DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng quản lý website thử nghiệm
CREATE TABLE websites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    domain VARCHAR(255) NOT NULL,
    allowed_users TEXT COMMENT 'Danh sách thành viên được xem báo cáo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng định nghĩa sự kiện được phép thống kê trên từng website
CREATE TABLE event_definitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    event_key VARCHAR(100) NOT NULL COMMENT 'Ví dụ: pageview, start, submit, download',
    event_name VARCHAR(150) NOT NULL,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng quản lý phiên người dùng (Không lưu IP đầy đủ để đảm bảo bảo mật)
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    session_token VARCHAR(100) NOT NULL,
    user_agent_info VARCHAR(255),
    created_at DATETIME NOT NULL,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng lưu trữ sự kiện (Hỗ trợ chống đếm đôi & phát hiện xung đột)
CREATE TABLE events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT NOT NULL,
    session_id INT NOT NULL,
    event_id_unique VARCHAR(100) NOT NULL UNIQUE COMMENT 'Mã sự kiện duy nhất từ client để chống trùng',
    event_type VARCHAR(100) NOT NULL,
    page_url VARCHAR(255) NOT NULL,
    payload_hash VARCHAR(64) NOT NULL COMMENT 'Mã băm nội dung payload để kiểm tra xung đột',
    payload_data TEXT COMMENT 'Dữ liệu sự kiện',
    event_timestamp DATETIME NOT NULL COMMENT 'Thời điểm theo múi giờ UTC+7',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (website_id) REFERENCES websites(id) ON DELETE CASCADE,
    FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bảng ghi nhận lỗi tiếp nhận (HTTP 409)
CREATE TABLE error_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_id INT,
    error_type VARCHAR(50) NOT NULL COMMENT 'Ví dụ: CONFLICT_EVENT, INVALID_DATA',
    message TEXT NOT NULL,
    attempted_payload TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dữ liệu mẫu ban đầu để kiểm thử và đăng nhập hệ thống
INSERT INTO admins (id, username, password) VALUES 
(1, 'admin', '123456');

INSERT INTO users (id, username, password, email, role) VALUES 
(1, 'user1', '123456', 'user1@gmail.com', 'user');

INSERT INTO websites (id, name, domain, allowed_users) VALUES 
(1, 'Website Thử Nghiệm 1 (E-Commerce)', 'site1.local', 'admin,manager1,user1'),
(2, 'Website Thử Nghiệm 2 (Landing Page)', 'site2.local', 'admin,manager2');

INSERT INTO event_definitions (website_id, event_key, event_name) VALUES 
(1, 'pageview', 'Xem trang sản phẩm'),
(1, 'search', 'Tìm kiếm sản phẩm'),
(1, 'download', 'Bấm tải tài liệu'),
(1, 'start', 'Bắt đầu đăng ký'),
(1, 'submit', 'Gửi biểu mẫu thành công'),
(2, 'pageview', 'Xem trang chủ'),
(2, 'search', 'Tìm kiếm thông tin'),
(2, 'start', 'Bắt đầu đăng ký'),
(2, 'submit', 'Hoàn tất đăng ký');