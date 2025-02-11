<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    /*
        1. ユーザーを登録する
        2. メールアドレス以外のユーザー情報を入力する
        3. ログインの処理を行う

        「メールアドレスを入力してください」というバリデーションメッセージが表示されること
    */
    public function test_admin_login_validation_email_required(): void
    {
        /* ユーザー登録 */
        $response = $this->get('/admin/register');
        $response = $this->post('/admin/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        /* ログイン処理 */
        $response = $this->post('/admin/login', [
            'email' => '',
            'password' => 'password123'
        ]);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください'
        ]);
    }

    /*
        1. ユーザーを登録する
        2. パスワード以外のユーザー情報を入力する
        3. ログインの処理を行う

        「パスワードを入力してください」というバリデーションメッセージが表示されること
    */
    public function test_admin_login_validation_password_required(): void
    {
        /* ユーザー登録 */
        $response = $this->get('/admin/register');
        $response = $this->post('/admin/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        /* ログイン処理 */
        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => ''
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください'
        ]);
    }

    /*
        1. ユーザーを登録する
        2. 誤ったメールアドレスのユーザー情報を入力する
        3. ログインの処理を行う

        「ログイン情報が登録されていません」というバリデーションメッセージが表示されること
    */
    public function test_admin_login_not_exist(): void
    {
        /* ユーザー登録 */
        $response = $this->get('/admin/register');
        $response = $this->post('/admin/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        /* ログイン処理 */
        $response = $this->post('/admin/login', [
            'email' => 'notexist@example.com',
            'password' => 'password123'
        ]);
        $response->assertSessionHasErrors([
            'email' => 'ログイン情報が登録されていません'
        ]);
    }
}
