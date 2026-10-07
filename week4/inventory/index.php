<?php
// db.php を読み込む(getDBConnection() と h() を使えるようにする)
// require_once = 同じファイルを2回読み込まない安全な書き方
require_once 'db.php';

// データベースに接続する
$conn = getDBConnection();

// ---- URLから「絞り込み条件」と「並び順」を受け取る ----
// 例: index.php?category=果物&sort=stock_asc
// ?? '' は「指定がなければ空文字にする」(警告を防ぐ)
$category = $_GET['category'] ?? '';
$sort     = $_GET['sort'] ?? '';

// ---- 並び順を決める ----
// ORDER BY の部分は、? プレースホルダーで渡せない。
// だから、ユーザーの入力をそのままSQLに入れず、
// 「決まった3つのどれか」に置き換える(SQLインジェクション対策)。
//   stock_asc  → 在庫が少ない順
//   stock_desc → 在庫が多い順
//   それ以外   → 登録日の新しい順(初期状態)
$orderBy = ($sort === 'stock_asc') ? 'stock ASC' : (($sort === 'stock_desc') ? 'stock DESC' : 'created_at DESC');

// ---- 商品を取得する ----
if ($category !== '') {
    // カテゴリが選ばれているとき: そのカテゴリの商品だけ取得
    // category = ? の ? は、あとで bind_param で値を入れる空欄
    $stmt = $conn->prepare("SELECT id, name, price, stock, category FROM products WHERE category = ? ORDER BY $orderBy");
    // "s" は文字列。? に $category を入れる
    $stmt->bind_param("s", $category);
} else {
    // カテゴリが未選択のとき: 全商品を取得(? がないので bind_param は不要)
    $stmt = $conn->prepare("SELECT id, name, price, stock, category FROM products ORDER BY $orderBy");
}
// SQLを実行する
$stmt->execute();
// 実行結果を受け取る(あとで1行ずつ取り出す)
$result = $stmt->get_result();

// ---- 絞り込み用のカテゴリ一覧を取得 ----
// DISTINCT = 重複を除く(「果物」が3件あっても1つだけ)
// 空のカテゴリは、選択肢に出さない
$cats = $conn->query("SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category <> ''");
?>
<!DOCTYPE html>
<html>
<head>
    <title>在庫管理システム</title>
    <!-- 見た目用のファイルを読み込む(なくてもエラーにはならない) -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>商品一覧</h1>
    <!-- 登録画面へのリンク -->
    <a href="create.php">新規商品登録</a>

    <!-- 絞り込み・並び替えのフォーム -->
    <!-- method="GET" なので、選んだ内容が URLの ?category=...&sort=... に付く -->
    <form method="GET">
        <select name="category">
            <option value="">すべてのカテゴリ</option>
            <!-- カテゴリを1つずつ取り出して、選択肢にする -->
            <?php while ($c = $cats->fetch_assoc()): ?>
            <!-- h() = XSS対策。selected = 今選んでいるカテゴリを選択状態にする -->
            <option value="<?php echo h($c['category']); ?>" <?php echo ($c['category'] === $category) ? 'selected' : ''; ?>>
                <?php echo h($c['category']); ?>
            </option>
            <?php endwhile; ?>
        </select>

        <select name="sort">
            <option value="">登録日の新しい順</option>
            <!-- 今の並び順を selected にして、画面を開き直しても選択が残るようにする -->
            <option value="stock_asc" <?php echo ($sort === 'stock_asc') ? 'selected' : ''; ?>>在庫が少ない順</option>
            <option value="stock_desc" <?php echo ($sort === 'stock_desc') ? 'selected' : ''; ?>>在庫が多い順</option>
        </select>

        <!-- 押すと、上の選択内容がURLに付いてこのページを開き直す -->
        <button type="submit">絞り込む</button>
    </form>

    <!-- 商品一覧の表 -->
    <table>
        <thead>
            <tr><th>ID</th><th>商品名</th><th>価格</th><th>在庫</th><th>カテゴリ</th><th>操作</th></tr>
        </thead>
        <tbody>
            <!-- 取得した商品を1行ずつ取り出して、表の行にする -->
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <!-- h() で囲むのは、表示するときのXSS対策 -->
                <td><?php echo h($row['id']); ?></td>
                <td><?php echo h($row['name']); ?></td>
                <td><?php echo h($row['price']); ?></td>
                <td><?php echo h($row['stock']); ?></td>
                <td><?php echo h($row['category']); ?></td>
                <td>
                    <!-- ?id=... で、どの商品かを edit.php に伝える -->
                    <a href="edit.php?id=<?php echo h($row['id']); ?>">編集</a>
                    <!-- confirm() = 確認ダイアログ。キャンセルなら false が返り、リンク先に移動しない -->
                    <a href="delete.php?id=<?php echo h($row['id']); ?>" onclick="return confirm('本当に削除しますか？')">削除</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
<?php
// 後片付け: 使い終わったSQLと接続を閉じる
$stmt->close();
$conn->close();
?>