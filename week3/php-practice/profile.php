<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>自己紹介</title>
<style>
  body { font-family: sans-serif; padding: 24px; max-width: 480px; }
  h1 { font-size: 22px; }
  ul { padding-left: 20px; }
  .message { background: #f0f4ff; padding: 12px; border-radius: 6px; margin-top: 16px; }
</style>
</head>
<body>

<?php
// ① 変数：自分の情報を定義する
$name  = "北村陽和";
$age   = 23;

// ② 配列：スキルをリストで管理する
$skills = ["野球", "バドミントン", "JavaScript", "PHP"];

// ③ 関数：スキルリストのHTMLを組み立てて返す（戻り値あり）
function buildSkillList($skills) {
    $html = "<ul>";
    foreach ($skills as $skill) {   // ④ ループ：配列を1件ずつ取り出す
        $html .= "<li>" . $skill . "</li>";
    }
    $html .= "</ul>";
    return $html;
}

// ⑤ 条件分岐：年齢によってメッセージを変える
if ($age < 20) {
    $message = "学生エンジニアとして活動中です！";
} else {
    $message = "社会人エンジニアを目指して勉強中です！";
}
?>

<h1><?php echo $name; ?></h1>
<p>年齢：<?php echo $age; ?>歳</p>
<h2>スキル</h2>
<?php echo buildSkillList($skills); ?>
<div class="message"><?php echo $message; ?></div>

</body>
</html>