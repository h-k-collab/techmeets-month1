<?php
$conn = new mysqli('127.0.0.1', 'root', 'root', 'myapp_db', 3306);

if ($conn->connect_error) {
    die("接続失敗: " . $conn->connect_error);
}

echo "データベースに接続しました！";
$conn->close();
?>