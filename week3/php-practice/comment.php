<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>コメント投稿</title>
</head>
<body>

<h1>コメント投稿フォーム</h1>

<form method="POST">
  <label>名前:</label>
  <input type="text" name="name"><br>
  <label>コメント:</label>
  <textarea name="comment"></textarea><br>
  <button type="submit">投稿する</button>
</form>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = $_POST["name"];
    $comment = $_POST["comment"];

    if ($name = "") {
        echo "名前を入力してください。";
    } else {
        echo "<p>" . $name . "さんのコメント:</p>";
        echo "<p>" . $comment . "</p>";
    }
}
?>

</body>
</html>