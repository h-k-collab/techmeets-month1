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
    // trim() で前後の空白のみの入力（実質空欄）も弾けるようにする
    $name    = trim($_POST["name"]);
    $comment = trim($_POST["comment"]);

    // 修正点1: "=" (代入演算子) だったのを "===" (厳密な比較演算子) に変更。
    // 元のコードは $name に "" を代入してしまい、その結果（常に false）で判定していたため
    // 名前が空でも常にelse側（投稿成功）に進んでしまっていた。
    // 修正点3: 名前だけでなくコメントも空かどうかチェックし、
    // 空のコメントが投稿できてしまう問題を解消。
    if ($name === "" || $comment === "") {
        echo "名前とコメントの両方を入力してください。";
    } else {
        // 修正点2: htmlspecialchars() でエスケープしてから出力することでXSS対策を追加。
        // ユーザー入力をそのままHTMLに埋め込んでいたため、
        // <script>タグなどを含む入力があると実行されてしまう脆弱性があった。
        echo "<p>" . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . "さんのコメント:</p>";
        echo "<p>" . htmlspecialchars($comment, ENT_QUOTES, 'UTF-8') . "</p>";
    }
}
?>

</body>
</html>