<?php
// functions/report_functions.php

/**
 * 1. Tổng hợp thống kê chi tiết theo ngày: tách riêng Lượt xem, Phiên, Tìm kiếm, Đăng ký, Tải về, Submit
 */
function getWebsiteSummary($pdo, $website_id, $date_from, $date_to)
{
    $sql = "SELECT 
                DATE(event_timestamp) as report_date,
                COUNT(CASE WHEN event_type = 'pageview' THEN 1 END) as total_pageviews,
                COUNT(DISTINCT session_id) as active_sessions,
                COUNT(CASE WHEN event_type = 'search' THEN 1 END) as total_searches,
                COUNT(CASE WHEN event_type = 'start' THEN 1 END) as total_starts,
                COUNT(CASE WHEN event_type = 'download' THEN 1 END) as total_downloads,
                COUNT(CASE WHEN event_type = 'submit' THEN 1 END) as total_submits,
                COUNT(CASE WHEN event_type != 'pageview' THEN 1 END) as total_actions
            FROM events 
            WHERE website_id = ? AND event_timestamp BETWEEN ? AND ?
            GROUP BY DATE(event_timestamp)
            ORDER BY report_date DESC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$website_id, $date_from, $date_to]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * 2. Lấy chỉ số tổng (KPI) toàn bộ khoảng thời gian lọc
 */
function getOverallStats($pdo, $website_id, $date_from, $date_to)
{
    $sql = "SELECT 
                COUNT(CASE WHEN event_type = 'pageview' THEN 1 END) as total_pageviews,
                COUNT(DISTINCT session_id) as active_sessions,
                COUNT(CASE WHEN event_type = 'search' THEN 1 END) as total_searches,
                COUNT(CASE WHEN event_type = 'start' THEN 1 END) as total_starts,
                COUNT(CASE WHEN event_type = 'download' THEN 1 END) as total_downloads,
                COUNT(CASE WHEN event_type = 'submit' THEN 1 END) as total_submits,
                COUNT(CASE WHEN event_type != 'pageview' THEN 1 END) as total_actions
            FROM events 
            WHERE website_id = ? AND event_timestamp BETWEEN ? AND ?";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$website_id, $date_from, $date_to]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
        'total_pageviews' => 0,
        'active_sessions' => 0,
        'total_searches' => 0,
        'total_starts' => 0,
        'total_downloads' => 0,
        'total_submits' => 0,
        'total_actions' => 0
    ];
}

/**
 * 3. Thống kê các nút bấm / liên kết được nhấp nhiều nhất (Top Clicked Actions)
 */
function getTopClickedActions($pdo, $website_id, $date_from, $date_to, $limit = 5)
{
    $sql = "SELECT 
                event_type,
                page_url,
                payload_data,
                COUNT(*) as click_count
            FROM events
            WHERE website_id = ? AND event_type != 'pageview' AND event_timestamp BETWEEN ? AND ?
            GROUP BY event_type, page_url, payload_data
            ORDER BY click_count DESC
            LIMIT " . intval($limit);

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$website_id, $date_from, $date_to]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = [];
    foreach ($rows as $row) {
        $payload = json_decode($row['payload_data'], true) ?: [];
        $actionName = $payload['element_text'] ?? $payload['action_name'] ?? $row['event_type'];

        $result[] = [
            'action_name' => $actionName,
            'event_type' => $row['event_type'],
            'page_url' => $row['page_url'],
            'click_count' => $row['click_count']
        ];
    }

    return $result;
}
?>