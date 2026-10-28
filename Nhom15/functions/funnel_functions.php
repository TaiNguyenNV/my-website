<?php
// functions/funnel_functions.php

// Phân tích chuỗi thao tác (Funnel Analysis: Xem trang -> Bắt đầu -> Hoàn tất trong cùng một phiên)
function getFunnelAnalysis($pdo, $website_id)
{
    $sql = "SELECT s.id as session_id, 
                   SUM(CASE WHEN e.event_type = 'pageview' THEN 1 ELSE 0 END) as has_view,
                   SUM(CASE WHEN e.event_type = 'start' THEN 1 ELSE 0 END) as has_start,
                   SUM(CASE WHEN e.event_type = 'submit' THEN 1 ELSE 0 END) as has_submit
            FROM sessions s
            LEFT JOIN events e ON s.id = e.session_id
            WHERE s.website_id = ?
            GROUP BY s.id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$website_id]);
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_sessions = count($sessions);
    $step_view = 0;
    $step_start = 0;
    $step_submit = 0;

    foreach ($sessions as $ses) {
        if ($ses['has_view'] > 0) {
            $step_view++;
        }
        if ($ses['has_view'] > 0 && $ses['has_start'] > 0) {
            $step_start++;
        }
        // Yêu cầu chuỗi đúng thứ tự và không thiếu bước
        if ($ses['has_view'] > 0 && $ses['has_start'] > 0 && $ses['has_submit'] > 0) {
            $step_submit++;
        }
    }

    return [
        "total_sessions" => $total_sessions,
        "step_view" => $step_view,
        "step_start" => $step_start,
        "step_submit" => $step_submit
    ];
}
?>