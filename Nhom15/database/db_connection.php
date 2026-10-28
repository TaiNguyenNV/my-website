<?php
// database/db_connection.php

$host = '127.0.0.1';
$dbname = 'nhom15'; // Đồng nhất tuyệt đối tên database là nhom15
$username = 'nhom15';
$password = '123456';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode([
        "status" => "error",
        "message" => "Lỗi kết nối cơ sở dữ liệu: " . $e->getMessage()
    ]));
}
?>