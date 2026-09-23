<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>コメントフォーム</title>
<style>
  body { font-family: sans-serif; padding: 24px; max-width: 480px; }
  label { display: block; margin-bottom: 4px; font-weight: bold; }
  input, textarea { width: 100%; padding: 8px; margin-bottom: 16px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
  button { padding: 8px 20px; font-size: 14px; cursor: pointer; }
  .result { background: #f0f4ff; padding: 16px; border-radius: 6px; margin-top: 24px; }
</style>
</head>
<body>

<h1>コメントフォーム</h1>

<!-- action を省略すると自分自身（form.php）に送信される -->
<form method="POST">
  <label>名前</label>
  <input type="text" name="username" placeholder="山田太郎">

  <label>コメント</label>
  <textarea name="comment" rows="3" placeholder="ひとことどうぞ"></textarea>

  <button type="submit">送信</button>
</form>

<?php
// $_POST にデータが入っているとき（＝フォームが送信されたとき）だけ処理する
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST["username"];
    $comment  = $_POST["comment"];

    echo '<div class="result">';
    echo "<p><strong>名前：</strong>" . $username . "</p>";
    echo "<p><strong>コメント：</strong>" . $comment . "</p>";
    echo '</div>';
}
?>

</body>
</html>