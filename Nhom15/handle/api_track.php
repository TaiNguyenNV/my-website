<?php
// handle/api_track.php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../database/db_connection.php';
require_once '../functions/event_functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => 405, "message" => "Phương thức không được hỗ trợ."]);
    exit();
}

$input_data = json_decode(file_get_contents('php://input'), true);

if (!$input_data) {
    http_response_code(400);
    echo json_encode(["status" => 400, "message" => "Dữ liệu JSON không hợp lệ."]);
    exit();
}

$result = processIncomingEvent($pdo, $input_data);

http_response_code($result['status']);
echo json_encode($result);
exit();
?>