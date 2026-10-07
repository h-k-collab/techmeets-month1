<?php
// db.php を読み込む(getDBConnection() を使えるようにする)
require_once 'db.php';

// URLの ?id=... から、削除する商品のIDを受け取る
$id = $_GET['id'] ?? '';

// idが指定されていない、または数字でない場合は、何もせず一覧に戻す
if ($id === '' || !ctype_digit($id)) {
    header('Location: index.php');
    exit;
}

$conn = getDBConnection();

// プリペアドステートメントで削除する
// WHERE id = ? を忘れると、全商品が消えるので必ず付ける
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
// "i" は整数。? に $id を入れる
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
$conn->close();

// 削除後は一覧ページに移動する
header('Location: index.php');
exit;
?>