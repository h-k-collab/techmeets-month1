# week4 振り返り

## 作ったもの

DockerでMySQLとphpMyAdminを起動し、PHPからデータベースに接続して、ユーザーの登録・一覧・編集・削除ができるユーザー管理システムを作成しました。


## 起動方法

```
cd week4
docker compose up -d
```

- phpMyAdmin: http://localhost:8080
- MySQL: `127.0.0.1:3306`（データベース名 `myapp_db`）

## 今週学んだこと
MySQLの基礎。データベースの利用で永続化、大量データ、同時アクセス、高速検索に対応可能。テーブル設定と正規化。
データベース操作の基本として、CRUDができることが重要。

## 難しかった点
・phpMyADminとの連動
・プリペアドステートメントの理解
・docker.desktopの役割

## 次に活かしたいこと
・SQLインジェクション対策
・SQL文でのプリペアドステートメントの使用
・htmlspecialchars()によるXSS対策
