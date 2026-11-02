<?php
// Ghi log vào logs/site2-tracker.log (mỗi dòng một JSON)
function track_event($type, $detail = '') {
    if (!is_dir(LOG_DIR)) { @mkdir(LOG_DIR, 0775, true); }
    $u = function_exists('current_user') ? current_user() : null;
    $row = [
        'time'   => date('c'),
        'ip'     => $_SERVER['REMOTE_ADDR'] ?? '',
        'user'   => $u ? $u['name'] : '',
        'role'   => $u ? $u['role'] : 'guest',
        'type'   => cut($type, 40),
        'detail' => cut($detail, 200),
        'ua'     => cut($_SERVER['HTTP_USER_AGENT'] ?? '', 150),
    ];
    @file_put_contents(LOG_DIR . '/site2-tracker.log', json_encode($row, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND | LOCK_EX);
}