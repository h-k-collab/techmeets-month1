<?php
// db.php を読み込む(getDBConnection() と h() を使えるようにする)
require_once 'db.php';

// URLの ?id=... から、編集する商品のIDを受け取る
$id    = $_GET['id'] ?? '';
$error = '';

$conn = getDBConnection();

// ---- 編集対象の商品を取得する(プリペアド) ----
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result  = $stmt->get_result();
$product = $result->fetch_assoc();   // 1件を連想配列で受け取る
$stmt->close();

// 商品が存在しない場合は、一覧に戻す
if (!$product) {
    $conn->close();
    header('Location: index.php');
    exit;
}

// ---- 「更新する」ボタンが押されたとき(POST) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $price    = trim($_POST['price'] ?? '');
    $stock    = trim($_POST['stock'] ?? '');
    $category = trim($_POST['category'] ?? '');

    // 未入力チェック
    if ($name === '' || $price === '' || $stock === '') {
        $error = '商品名・価格・在庫数は必須です。';
    } elseif (!is_numeric($price) || $price < 0 || !ctype_digit($stock)) {
        $error = '価格は0以上の数値、在庫数は0以上の整数で入力してください。';
    } else {
        // ---- 更新(プリペアド) ----
        // WHERE id = ? を忘れると、全商品が書き換わるので必ず付ける
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, stock = ?, category = ? WHERE id = ?");
        // 型: s=文字列, d=小数, i=整数, s=文字列, i=整数(id) → "sdisi"
        $stmt->bind_param("sdisi", $name, $price, $stock, $category, $id);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            header('Location: index.php');
            exit;
        } else {
            $error = '更新に失敗しました: ' . $stmt->error;
            $stmt->close();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>商品編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>商品編集</h1>
    <a href="index.php">← 一覧に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo h($error); ?></p>
    <?php endif; ?>

    <!-- value には「送信された値」か「DBの現在の値」を表示する -->
    <form method="POST">
        <label>商品名:
            <input type="text" name="name"
                value="<?php echo h($_POST['name'] ?? $product['name']); ?>">
        </label><br>
        <label>価格:
            <input type="number" name="price" step="0.01" min="0"
                value="<?php echo h($_POST['price'] ?? $product['price']); ?>">
        </label><br>
        <label>在庫数:
            <input type="number" name="stock" min="0"
                value="<?php echo h($_POST['stock'] ?? $product['stock']); ?>">
        </label><br>
        <label>カテゴリ:
            <input type="text" name="category"
                value="<?php echo h($_POST['category'] ?? $product['category']); ?>">
        </label><br>
        <button type="submit">更新する</button>
    </form>
</body>
</html>