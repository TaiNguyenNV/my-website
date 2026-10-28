<?php
// tools/seed_data.php
require_once '../database/db_connection.php';

echo "<h2>Đang tiến hành sinh dữ liệu giả lập (khoảng 200 sự kiện chuẩn hóa)...</h2>";

// Xóa dữ liệu cũ nếu muốn làm mới
$pdo->exec("DELETE FROM events");
$pdo->exec("DELETE FROM error_logs");
$pdo->exec("DELETE FROM sessions");

$websites = [1, 2];
// Tỷ lệ phân bố sự kiện thực tế: Xem trang -> Tìm kiếm -> Bấm đăng ký -> Tải về -> Gửi form
$event_types = ['pageview', 'pageview', 'search', 'start', 'download', 'submit'];
$pages = ['/index.php', '/product.php', '/checkout.php', '/about.php', '/contact.php', '/download.php'];

$action_names = [
    'search' => ['🔍 Bấm tìm kiếm sản phẩm', '🔍 Tìm tài liệu hướng dẫn', '🔍 Tìm kiếm khuyến mãi'],
    'start' => ['📝 Bấm nút đăng ký dịch vụ', '📝 Bắt đầu tạo tài khoản', '📝 Bấm đăng ký nhận ưu đãi'],
    'download' => ['📥 Bấm tải tài liệu hướng dẫn', '📥 Bấm tải Catalogue sản phẩm', '📥 Bấm tải báo giá PDF'],
    'submit' => ['🚀 Gửi biểu mẫu đăng ký', '🚀 Hoàn tất khảo sát', '🚀 Gửi thông tin tư vấn'],
    'pageview' => ['Xem trang web']
];

$count = 0;
for ($i = 1; $i <= 200; $i++) {
    $ws_id = $websites[array_rand($websites)];
    $session_token = 'sess_seed_' . rand(1, 25);
    $event_id_unique = 'evt_seed_' . $i . '_' . rand(1000, 9999);
    $event_type = $event_types[array_rand($event_types)];
    $page_url = 'http://site' . $ws_id . '.local' . $pages[array_rand($pages)];

    // Tạo thời gian ngẫu nhiên trong 30 ngày qua (bao gồm qua nửa đêm)
    $days_ago = rand(0, 30);
    $hour = rand(0, 23);
    $minute = rand(0, 59);
    $second = rand(0, 59);
    $event_timestamp = date('Y-m-d H:i:s', strtotime("-$days_ago days $hour:$minute:$second"));

    // Kiểm tra hoặc tạo session
    $stmt = $pdo->prepare("SELECT id FROM sessions WHERE website_id = ? AND session_token = ?");
    $stmt->execute([$ws_id, $session_token]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        $stmt = $pdo->prepare("INSERT INTO sessions (website_id, session_token, user_agent_info, created_at) VALUES (?, ?, 'Seed Script Agent', ?)");
        $stmt->execute([$ws_id, $session_token, $event_timestamp]);
        $session_id = $pdo->lastInsertId();
    } else {
        $session_id = $session['id'];
    }

    $action_text = $action_names[$event_type][array_rand($action_names[$event_type])];
    $payload_data = [
        "element_text" => $action_text,
        "device" => (rand(0, 1) ? "desktop" : "mobile"),
        "screen_resolution" => (rand(0, 1) ? "1920x1080" : "390x844")
    ];
    $payload_json = json_encode($payload_data);
    $payload_hash = hash('sha256', $payload_json);

    try {
        $stmt = $pdo->prepare("INSERT INTO events (website_id, session_id, event_id_unique, event_type, page_url, payload_hash, payload_data, event_timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ws_id, $session_id, $event_id_unique, $event_type, $page_url, $payload_hash, $payload_json, $event_timestamp]);
        $count++;
    } catch (PDOException $e) {
        // Bỏ qua lỗi trùng khóa nếu có
    }
}

echo "<p style='color: green; font-weight: bold;'>Đã sinh thành công $count sự kiện giả lập (bao gồm Tìm kiếm, Bấm đăng ký, Tải về, Gửi biểu mẫu) vào cơ sở dữ liệu!</p>";
echo "<a href='../views/dashboard.php' style='padding: 10px 20px; background: #4f46e5; color: white; text-decoration: none; border-radius: 6px; display: inline-block; margin-top: 20px;'>Đi tới Dashboard Báo cáo</a>";
?>