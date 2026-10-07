<?php
// データベース接続情報
$host = '127.0.0.1';
$dbname = 'myapp_db';
$username = 'root';
$password = 'root';

// MySQLiで接続
$conn = new mysqli($host, $username, $password, $dbname);

// 接続エラーチェック
if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
    // die(): 処理を即座に止めてメッセージを表示する関数
}

// 文字コード設定（日本語が文字化けしないために必須）
$conn->set_charset("utf8mb4");

echo "データベースに接続しました";
?>