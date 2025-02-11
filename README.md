# coachtech 勤怠管理アプリ

## 環境構築

1.  はじめに
```
$ コマンドラインで実行するコマンドであることをあらわす
# PHPコンテナで実行するコマンドであることをあらわす
```

2.  リポジトリのコピー
```
$ git clone git@github.com:s-hatta/test-attendance-system.git
```

3.  .envファイルの設定
```
$ cd test-attendance-system/
$ cp src/.env.example src/.env
```

4.  .envファイルを編集（+は追加する行、-は削除する行）
```
// 中略
- # DB_HOST=127.0.0.1
- # DB_PORT=3306
- # DB_DATABASE=laravel
- # DB_USERNAME=root
- # DB_PASSWORD=
+ DB_HOST=mysql
+ DB_PORT=3306
+ DB_DATABASE=laravel_db
+ DB_USERNAME=laravel_user
+ DB_PASSWORD=laravel_pass
```

5.  Dockerビルド
```
$ docker compose up -d --build
```

6.  Laravelのパッケージインストール
```
$ docker compose exec php bash
# composer install
```

7. パッケージインストール
```
# npm install
```

8.  アプリケーションキー作成
```
# php artisan key:generate
```

9.  マイグレーション＆シーディング
```
# php artisan migrate:fresh --seed
# exit
```

10. docker再起動
```
$ docker compose restart
```

## 使用技術(実行環境)
- PHP 8.3.13
- Lalavel Framework 11.36.1
    - fortify 1.25
- MySQL 8.0.40
- phpMyAdmin 5.2.2
- nginx 1.26.2
- mailhog
- Tailwind CSS
- Vite

## ER図
![ER](https://github.com/user-attachments/assets/85d13d84-8910-4931-8fa4-ada744381900)

## URL
- 開発環境（一般ユーザー）：http://localhost/
- 開発環境（管理者）：http://localhost/admin/login
- phpMyAdmin：http://localhost:8080/
- MailHog：http://localhost:8025/

## テスト用ユーザー
### 一般ユーザー
 - メールアドレス：test@example.com
 - パスワード：password
### 管理者
 - メールアドレス：admin@example.com
 - パスワード：password

## テストの実行手順
※テスト実行後はマイグレーションとシーディングをおこなうこと
1.  PHPUnit  
```
# php artisan test
```

