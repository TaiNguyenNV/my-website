<?php
// handle/export_report.php
session_start();
require_once '../database/db_connection.php';
require_once '../functions/auth_functions.php';
require_once '../functions/report_functions.php';

requireLogin();

$website_id = intval($_GET['website_id'] ?? 1);
$username = $_SESSION['user'];

// Kiểm tra quyền xem báo cáo
if (!checkWebsitePermission($pdo, $website_id, $username)) {
    die("Bạn không có quyền xuất báo cáo cho website này.");
}

$date_from = $_GET['date_from'] ?? date('Y-m-d', strtotime('-30 days'));
$date_to = $_GET['date_to'] ?? date('Y-m-d');

$summary = getWebsiteSummary($pdo, $website_id, $date_from, $date_to . ' 23:59:59');

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=baocaothongke_website_' . $website_id . '.csv');

$output = fopen('php://output', 'w');
// Ghi BOM để Excel đọc đúng tiếng Việt
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, [
    'Ngày báo cáo',
    'Lượt xem trang (Pageviews)',
    'Số phiên (Active Sessions)',
    'Lượt tìm kiếm (Search)',
    'Lượt bấm đăng ký (Start)',
    'Lượt tải tài liệu (Download)',
    'Lượt hoàn tất (Submit)',
    'Tổng số thao tác'
]);

foreach ($summary as $row) {
    fputcsv($output, [
        $row['report_date'],
        $row['total_pageviews'],
        $row['active_sessions'],
        $row['total_searches'],
        $row['total_starts'],
        $row['total_downloads'],
        $row['total_submits'],
        $row['total_actions']
    ]);
}

fclose($output);
exit();
?>