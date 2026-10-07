<?php
require_once 'db.php';

$id = $_GET['id'] ?? '';

// idが指定されていない場合は一覧に戻す
if ($id === '') {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

// 削除後は一覧ページに移動
header('Location: index.php');
exit;
?>