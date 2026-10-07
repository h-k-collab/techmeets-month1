<?php
require_once 'db.php';

$id    = $_GET['id'] ?? '';
$error = '';

$conn = getDBConnection();

// 編集対象のユーザーを取得（GETパラメータのid で検索）
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

// ユーザーが存在しない場合は一覧に戻す
if (!$user) {
    $conn->close();
    header('Location: index.php');
    exit;
}

// フォームが送信されたとき（POSTリクエスト）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $age      = $_POST['age'] ?? '';

    if ($username === '' || $email === '') {
        $error = 'ユーザー名とメールアドレスは必須です。';
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, age = ? WHERE id = ?");
        $stmt->bind_param("ssii", $username, $email, $age, $id);

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
    <title>ユーザー編集</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ユーザー編集</h1>
    <a href="index.php">← 一覧に戻る</a>

    <?php if ($error): ?>
    <p style="color: red;"><?php echo h($error); ?></p>
    <?php endif; ?>

    <!-- value には「POSTで送られた値」か「DBから取得した現在の値」を表示する -->
    <form method="POST">
        <label>ユーザー名:
            <input type="text" name="username"
                value="<?php echo h($_POST['username'] ?? $user['username']); ?>">
        </label><br>
        <label>メール:
            <input type="email" name="email"
                value="<?php echo h($_POST['email'] ?? $user['email']); ?>">
        </label><br>
        <label>年齢:
            <input type="number" name="age"
                value="<?php echo h($_POST['age'] ?? $user['age']); ?>">
        </label><br>
        <button type="submit">更新する</button>
    </form>
</body>
</html>