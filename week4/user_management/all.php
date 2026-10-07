<?php
// データベース接続情報
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'myapp_db');
define('DB_USER', 'root');
define('DB_PASS', 'root');

// データベース接続
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if ($conn->connect_error) {
        die("接続失敗: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");
    return $conn;
}
?>

<?php
require_once 'db.php';
$conn = getDBConnection();

// 全ユーザーを取得
$query = "SELECT id, username, email, age, created_at FROM users ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>ユーザー管理システム</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>ユーザー一覧</h1>
    <a href="create.php">新規ユーザー登録</a>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ユーザー名</th>
                <th>メール</th>
                <th>年齢</th>
                <th>登録日</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo $row['age']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <a href="edit.php?id=<?php echo $row['id']; ?>">編集</a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('本当に削除しますか？')">削除</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>

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
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>ユーザー名:
            <input type="text" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
        </label><br>
        <label>メール:
            <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
        </label><br>
        <label>年齢:
            <input type="number" name="age" value="<?php echo htmlspecialchars($_POST['age'] ?? ''); ?>">
        </label><br>
        <button type="submit">登録する</button>
    </form>
</body>
</html>

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
    <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <!-- value には「POSTで送られた値」か「DBから取得した現在の値」を表示する -->
    <form method="POST">
        <label>ユーザー名:
            <input type="text" name="username"
                value="<?php echo htmlspecialchars($_POST['username'] ?? $user['username']); ?>">
        </label><br>
        <label>メール:
            <input type="email" name="email"
                value="<?php echo htmlspecialchars($_POST['email'] ?? $user['email']); ?>">
        </label><br>
        <label>年齢:
            <input type="number" name="age"
                value="<?php echo htmlspecialchars($_POST['age'] ?? $user['age']); ?>">
        </label><br>
        <button type="submit">更新する</button>
    </form>
</body>
</html>

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
