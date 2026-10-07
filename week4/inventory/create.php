<?php
// db.php を読み込む(getDBConnection() と h() を使えるようにする)
require_once 'db.php';

// エラーメッセージを入れる変数(最初は空)
$error = '';

// 「登録する」ボタンが押されたときだけ、この中に入る(POSTリクエスト)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームの値を受け取る。trim() で前後の空白を取り除く
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? '');
    $category = trim($_POST['category'] ?? '');

    // 未入力チェック: 商品名・価格・在庫は必須(テーブルが NOT NULL のため)
    if ($name === '' || $price === '' || $stock === '') {
        $error = '商品名・価格・在庫数は必須です。';
    } elseif (!is_numeric($price) || $price < 0 || !ctype_digit($stock)) {
        // 価格は0以上の数値、在庫は0以上の整数だけ許可する
        $error = '価格は0以上の数値、在庫数は0以上の整数で入力してください。';
    } else {
        $conn = getDBConnection();

        // ? は、あとで値を入れる空欄(プリペアドステートメント)
        $stmt = $conn->prepare("INSERT INTO products (name, price, stock, category) VALUES (?, ?, ?, ?)");

        // 型の指定: s=文字列, d=小数, i=整数, s=文字列 → "sdis"
        $stmt->bind_param("sdis", $name, $price, $stock, $category);

        if ($stmt->execute()) {
            // 成功: 後片付けをして、一覧ページに移動する
            $stmt->close();
            $conn->close();
            header('Location: index.php');
            exit;
        } else {
            // 失敗: エラーを画面に出す
            $error = '登録に失敗しました: ' . $stmt->error;
            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>商品登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>新規商品登録</h1>
    <a href="index.php">← 一覧に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo h($error); ?></p>
    <?php endif; ?>

    <!-- エラーで戻ってきたとき、入力済みの値が消えないよう value に入れ直す -->
    <form method="POST">
        <label>商品名:
            <input type="text" name="name" value="<?php echo h($_POST['name'] ?? ''); ?>">
        </label><br>
        <label>価格:
            <input type="number" name="price" step="0.01" min="0" value="<?php echo h($_POST['price'] ?? ''); ?>">
        </label><br>
        <label>在庫数:
            <input type="number" name="stock" min="0" value="<?php echo h($_POST['stock'] ?? '0'); ?>">
        </label><br>
        <label>カテゴリ:
            <input type="text" name="category" value="<?php echo h($_POST['category'] ?? ''); ?>">
        </label><br>
        <button type="submit">登録する</button>
    </form>
</body>
</html>