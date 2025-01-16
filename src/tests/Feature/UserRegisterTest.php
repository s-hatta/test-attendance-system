<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserRegisterTest extends TestCase
{
    use RefreshDatabase;
    
    /*
        1. 名前以外のユーザー情報を入力する
        2. 会員登録の処理を行う
        
        「お名前を入力してください」というバリデーションメッセージが表示されること
    */
    public function test_user_register_validation_name_required()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }
    
    /*
        1. メールアドレス以外のユーザー情報を入力する
        2. 会員登録の処理を行う
        
        「メールアドレスを入力してください」というバリデーションメッセージが表示されること
    */
    public function test_user_register_validation_email_required()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }
    
    /*
        1. パスワードを8文字未満にし、ユーザー情報を入力する
        2. 会員登録の処理を行う
        
        「パスワードは8文字以上で入力してください」というバリデーションメッセージが表示されること
    */
    public function test_user_register_validation_password_min()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }
    
    /*
        1. 確認用のパスワードとパスワードを一致させず、ユーザー情報を入力する
        2 . 会員登録の処理を行う
        
        「パスワードと一致しません」というバリデーションメッセージが表示されること
    */
    public function test_user_register_validation_password_confirmation()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password456',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードと一致しません',
        ]);
    }
    
    /*
        1. パスワード以外のユーザー情報を入力する
        2. 会員登録の処理を行う
        
        「パスワードを入力してください」というバリデーションメッセージが表示されること
    */
    public function test_user_register_validation_password_required()
    {
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }
    
    /*
        1. ユーザー情報を入力する
        2. 会員登録の処理を行う
        
        データベースに登録したユーザー情報が保存されること
    */
    public function test_user_register_success()
    {
        /* 会員登録 */
        $response = $this->get('/register');
        $response = $this->post('/register', [
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        /* 会員情報が登録されているか */
        $this->assertDatabaseHas(User::Class, [
            'name' => 'test',
            'email' => 'test@example.com'
        ]);
    }
}
