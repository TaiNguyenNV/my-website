<?php
// functions/event_functions.php

// Xử lý sự kiện đến, chống đếm trùng (Deduplication) và kiểm tra xung đột (HTTP 409)
function processIncomingEvent($pdo, $data)
{
    $website_id = $data['website_id'] ?? null;
    $session_token = $data['session_token'] ?? null;
    $event_id_unique = $data['event_id_unique'] ?? null;
    $event_type = $data['event_type'] ?? null;
    $page_url = $data['page_url'] ?? '';
    $payload_data = $data['payload_data'] ?? []; // Không chứa input, mật khẩu, IP đầy đủ

    if (!$website_id || !$session_token || !$event_id_unique || !$event_type) {
        return ["status" => 400, "message" => "Thiếu thông tin bắt buộc sự kiện."];
    }

    // 1. Kiểm tra hoặc tạo phiên (session) mới
    $stmt = $pdo->prepare("SELECT id FROM sessions WHERE website_id = ? AND session_token = ?");
    $stmt->execute([$website_id, $session_token]);
    $session = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$session) {
        $stmt = $pdo->prepare("INSERT INTO sessions (website_id, session_token, user_agent_info, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$website_id, $session_token, $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown']);
        $session_id = $pdo->lastInsertId();
    } else {
        $session_id = $session['id'];
    }

    // Tính mã băm payload để kiểm tra xung đột
    $payload_json = json_encode($payload_data);
    $payload_hash = hash('sha256', $payload_json);
    $event_timestamp = $data['timestamp'] ?? date('Y-m-d H:i:s');

    // 2. Kiểm tra xem event_id_unique đã tồn tại chưa (Yêu cầu 1)
    $stmt = $pdo->prepare("SELECT * FROM events WHERE event_id_unique = ?");
    $stmt->execute([$event_id_unique]);
    $existing_event = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($existing_event) {
        // Cùng mã sự kiện nhưng khác nội dung payload -> Xung đột (HTTP 409)
        if ($existing_event['payload_hash'] !== $payload_hash) {
            $log_stmt = $pdo->prepare("INSERT INTO error_logs (website_id, error_type, message, attempted_payload) VALUES (?, 'CONFLICT_EVENT', ?, ?)");
            $log_stmt->execute([$website_id, "Xung đột: Trùng mã sự kiện $event_id_unique nhưng khác nội dung payload.", $payload_json]);

            return ["status" => 409, "message" => "Xung đột dữ liệu: Mã sự kiện đã tồn tại với nội dung khác nhau."];
        }

        // Cùng mã và cùng nội dung -> Gửi lại (Deduplication), không tăng số đếm
        return ["status" => 200, "message" => "Sự kiện đã tồn tại (Đã bỏ qua đếm trùng).", "duplicated" => true];
    }

    // 3. Lưu sự kiện mới vào CSDL
    $stmt = $pdo->prepare("INSERT INTO events (website_id, session_id, event_id_unique, event_type, page_url, payload_hash, payload_data, event_timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$website_id, $session_id, $event_id_unique, $event_type, $page_url, $payload_hash, $payload_json, $event_timestamp]);

    return ["status" => 201, "message" => "Ghi nhận sự kiện thành công.", "duplicated" => false];
}
?>