<?php
require_once 'db.php';

$error = '';

// フォームが送信されたとき（POSTリクエスト）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $age      = $_POST['age'] ?? '';

    if ($username === '' || $email === '') {
        $error = 'ユーザー名とメールアドレスは必須です。';
    } else {
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO users (username, email, age) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $username, $email, $age);

        if ($stmt->execute()) {
            $stmt->close();
            $conn->close();
            // 登録成功したら一覧ページに移動する
            header('Location: index.php');
            exit;
        } else {
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
    <title>ユーザー登録</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>新規ユーザー登録</h1>
    <a href="index.php">← 一覧に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo h($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>ユーザー名:
            <input type="text" name="username" value="<?php echo h($_POST['username'] ?? ''); ?>">
        </label><br>
        <label>メール:
            <input type="email" name="email" value="<?php echo h($_POST['email'] ?? ''); ?>">
        </label><br>
        <label>年齢:
            <input type="number" name="age" value="<?php echo h($_POST['age'] ?? ''); ?>">
        </label><br>
        <button type="submit">登録する</button>
    </form>
</body>
</html>
