<?php
// データベース接続情報
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'myapp_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// データベース接続
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}
function h($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>