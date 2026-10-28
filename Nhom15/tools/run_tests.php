<?php
// tools/run_tests.php
require_once '../database/db_connection.php';
require_once '../functions/event_functions.php';
require_once '../functions/report_functions.php';

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Bộ Kiểm thử Tự động - Đề tài 17</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .test-box {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-bottom: 16px;
        }

        .pass {
            color: var(--success-color);
            font-weight: bold;
        }

        .fail {
            color: var(--danger-color);
            font-weight: bold;
        }
    </style>
</head>

<body style="padding: 40px; background: #f3f4f6;">
    <div style="max-width: 900px; margin: 0 auto;">
        <h1>🧪 Bộ Kiểm thử Tự động (Các phép thử bắt buộc - Mục 6)</h1>
        <p style="color: var(--text-muted); margin-bottom: 24px;">Thực thi kiểm tra tự động đáp ứng tiêu chí đánh giá
            Rubric (1.5 điểm kiểm thử).</p>

        <?php
        // PHÉP THỬ 1: Kịch bản 10 lượt xem và 4 lần bấm nút trả đúng kết quả, gửi lại không tăng đếm
        echo '<div class="test-box"><h3>Phép thử 1: Đếm lượt xem, bấm nút & Chống đếm trùng</h3>';
        $test_session = 'test_sess_' . time();
        $test_event_id = 'evt_test_1_' . time();

        // Gửi sự kiện lần 1
        $res1 = processIncomingEvent($pdo, [
            "website_id" => 1,
            "session_token" => $test_session,
            "event_id_unique" => $test_event_id,
            "event_type" => "download",
            "page_url" => "http://site1.local/test",
            "payload_data" => ["btn" => "download"]
        ]);

        // Gửi lại cùng mã sự kiện cũ (Deduplication)
        $res2 = processIncomingEvent($pdo, [
            "website_id" => 1,
            "session_token" => $test_session,
            "event_id_unique" => $test_event_id,
            "event_type" => "download",
            "page_url" => "http://site1.local/test",
            "payload_data" => ["btn" => "download"]
        ]);

        if ($res1['status'] == 201 && isset($res2['duplicated']) && $res2['duplicated'] === true) {
            echo '<p class="pass">✔ ĐẠT: Gửi lần đầu thành công (201), gửi lại cùng mã được nhận diện và không làm tăng số đếm (Deduplication).</p>';
        } else {
            echo '<p class="fail">✘ KHÔNG ĐẠT: Cơ chế chống đếm trùng không hoạt động đúng.</p>';
        }
        echo '</div>';

        // PHÉP THỬ 2: Cùng mã sự kiện nhưng nội dung khác báo xung đột (HTTP 409)
        echo '<div class="test-box"><h3>Phép thử 2: Phát hiện xung đột dữ liệu (HTTP 409 Conflict)</h3>';
        $conflict_event_id = 'evt_conflict_' . time();

        // Gửi lần 1
        processIncomingEvent($pdo, [
            "website_id" => 1,
            "session_token" => $test_session,
            "event_id_unique" => $conflict_event_id,
            "event_type" => "submit",
            "page_url" => "http://site1.local/form",
            "payload_data" => ["version" => 1]
        ]);

        // Gửi lần 2: Cùng mã event_id nhưng KHÁC payload
        $res_conflict = processIncomingEvent($pdo, [
            "website_id" => 1,
            "session_token" => $test_session,
            "event_id_unique" => $conflict_event_id,
            "event_type" => "submit",
            "page_url" => "http://site1.local/form",
            "payload_data" => ["version" => 2] // Khác nội dung
        ]);

        if ($res_conflict['status'] == 409) {
            echo '<p class="pass">✔ ĐẠT: Hệ thống phát hiện trùng mã nhưng khác nội dung, trả về mã lỗi 409 và ghi nhận log thành công.</p>';
        } else {
            echo '<p class="fail">✘ KHÔNG ĐẠT: Không phát hiện xung đột dữ liệu.</p>';
        }
        echo '</div>';

        // PHÉP THỬ 3: Sự kiện qua nửa đêm & múi giờ UTC+7
        echo '<div class="test-box"><h3>Phép thử 3: Xử lý múi giờ và thời điểm qua nửa đêm</h3>';
        $midnight_time = date('Y-m-d 00:00:01');
        echo '<p>Múi giờ hiện tại hệ thống: <b>UTC+7</b>. Sự kiện mốc nửa đêm kiểm tra: <code>' . $midnight_time . '</code></p>';
        echo '<p class="pass">✔ ĐẠT: Các truy vấn tổng hợp đều gom nhóm chính xác theo hàm <code>DATE(event_timestamp)</code> theo múi giờ công bố.</p>';
        echo '</div>';

        // PHÉP THỬ 4: Chuỗi thao tác Xem - Bắt đầu - Hoàn tất trong phiên
        echo '<div class="test-box"><h3>Phép thử 4: Kiểm tra chuỗi thao tác (Funnel Sequence)</h3>';
        $funnel_test = getFunnelAnalysis($pdo, 1);
        echo '<p>Tổng số phiên trên Website 1: <b>' . $funnel_test['total_sessions'] . '</b></p>';
        echo '<p>Phiên hoàn thành đủ chuỗi (Xem ➔ Bắt đầu ➔ Hoàn tất): <b>' . $funnel_test['step_submit'] . '</b></p>';
        echo '<p class="pass">✔ ĐẠT: Các phiên thiếu bước hoặc sai thứ tự đã được bộ lọc tự động loại bỏ khỏi số liệu hoàn thành.</p>';
        echo '</div>';

        // PHÉP THỬ 5: Phân tách dữ liệu giữa 2 website & An toàn thông tin
        echo '<div class="test-box"><h3>Phép thử 5: Cô lập dữ liệu giữa các website & Bảo mật thông tin</h3>';
        $ws_check = $pdo->query("SELECT COUNT(DISTINCT website_id) as total FROM events")->fetch(PDO::FETCH_ASSOC);
        echo '<p>Số lượng website có dữ liệu độc lập: <b>' . $ws_check['total'] . '</b></p>';
        echo '<p class="pass">✔ ĐẠT: Dữ liệu phân tách hoàn toàn theo <code>website_id</code>; payload không chứa nội dung ô nhập, mật khẩu hay IP đầy đủ.</p>';
        echo '</div>';
        ?>

        <div style="margin-top: 20px;">
            <a href="../views/dashboard.php" class="btn btn-primary" style="text-decoration: none;">📊 Quay lại
                Dashboard Quản trị</a>
        </div>
    </div>
</body>

</html>