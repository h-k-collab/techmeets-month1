<?php
// 【変更】htmlspecialchars(..., ENT_QUOTES, 'UTF-8') が何度も出てくるため、関数 h() にまとめた。
// 出力するときは h($変数) と書くだけでXSS対策（HTMLエスケープ）ができる。
// エスケープの設定を変えたいときも、この関数の中を1か所直すだけで済む。
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// エラーメッセージの格納用配列と、送信成功したかどうかのフラグ
$errors = [];
$submitted = false;

// フォームの初期値（未送信時・エラー時に空欄/入力値を保持するため）
$name    = "";
$email   = "";
$subject = "";
$message = "";

// フォームが送信されたとき（POSTリクエスト）だけバリデーションを行う
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // 各項目を取得し、前後の空白を除去
    $name    = trim($_POST["name"] ?? "");
    $email   = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    // 名前の必須チェック
    if ($name === "") {
        $errors[] = "名前を入力してください。";
    }
    // メールアドレスの必須チェック＋形式チェック
    if ($email === "") {
        $errors[] = "メールアドレスを入力してください。";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "メールアドレスの形式が正しくありません。";
    }
    // 件名の必須チェック
    if ($subject === "") {
        $errors[] = "件名を入力してください。";
    }
    // メッセージの必須チェック
    if ($message === "") {
        $errors[] = "メッセージを入力してください。";
    }

    // エラーが一つもなければ送信成功とみなし、確認画面を表示する
    if (empty($errors)) {
        $submitted = true;
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>お問い合わせフォーム</title>
<style>
  body { font-family: sans-serif; padding: 24px; max-width: 480px; }
  label { display: block; margin-bottom: 4px; font-weight: bold; }
  input, textarea { width: 100%; padding: 8px; margin-bottom: 16px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; }
  button { padding: 8px 20px; font-size: 14px; cursor: pointer; }
  .result { background: #f0f4ff; padding: 16px; border-radius: 6px; margin-top: 24px; }
  .errors { background: #fff0f0; border: 1px solid #f5b5b5; padding: 12px 16px; border-radius: 6px; margin-bottom: 16px; }
  .errors p { margin: 4px 0; color: #c00; }
</style>
</head>
<body>

<h1>お問い合わせフォーム</h1>

<?php if ($submitted): ?>

<!-- 送信成功時：入力内容をそのまま確認画面として表示する -->
<div class="result">
  <h2>以下の内容で送信されました</h2>
  <!-- h()（中身は htmlspecialchars）でエスケープしてから出力することでXSSを防止 -->
  <!-- 【変更】htmlspecialchars(..., ENT_QUOTES, 'UTF-8') → h() に置き換え -->
  <p><strong>名前：</strong><?php echo h($name); ?></p>
  <p><strong>メールアドレス：</strong><?php echo h($email); ?></p>
  <p><strong>件名：</strong><?php echo h($subject); ?></p>
  <!-- nl2br で改行を <br> に変換してから表示（メッセージは複数行を想定） -->
  <!-- 順番は変えていない：先に h() でエスケープ → その後 nl2br で <br> を付ける -->
  <p><strong>メッセージ：</strong><?php echo nl2br(h($message)); ?></p>
</div>

<?php else: ?>

<!-- 未送信、またはバリデーションエラー時：エラー内容があれば一覧表示 -->
<?php if (!empty($errors)): ?>
<div class="errors">
  <?php foreach ($errors as $error): ?>
  <!-- 【変更】htmlspecialchars → h() に置き換え -->
  <p><?php echo h($error); ?></p>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- 入力フォーム本体：エラー時は入力済みの値を value/中身に復元する -->
<!-- 【変更】value や textarea の中身も htmlspecialchars → h() に置き換え -->
<form method="POST">
  <label>名前</label>
  <input type="text" name="name" value="<?php echo h($name); ?>">

  <label>メールアドレス</label>
  <input type="text" name="email" value="<?php echo h($email); ?>">

  <label>件名</label>
  <input type="text" name="subject" value="<?php echo h($subject); ?>">

  <label>メッセージ</label>
  <textarea name="message" rows="5"><?php echo h($message); ?></textarea>

  <button type="submit">送信</button>
</form>

<?php endif; ?>

</body>
</html>
