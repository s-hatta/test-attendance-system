<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Enums\AttendanceStatus;
use App\Enums\CorrectionStatus;
use Carbon\Carbon;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Admin $admin;
    private Attendance $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        /* テストユーザー作成 */
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /* 管理者作成 */
        $this->admin = Admin::create([
            'name' => 'test_admin',
            'email' => 'test@admin.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        /* 勤怠データ作成 */
        $now = Carbon::now();
        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $now->format('Y-m-d'),
            'clock_in_at' => $now->copy()->setHour(9),
            'clock_out_at' => $now->copy()->setHour(18),
            'status' => AttendanceStatus::LEFT,
        ]);
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 出勤時間を退勤時間より後に設定する
        4. 保存処理をする

        「出勤時間もしくは退勤時間が不適切な値です」というバリデーションメッセージが表示されること
    */
    public function test_validates_clock_in_time_not_after_clock_out()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細ページを開き、出勤時間を退勤時間より後に設定する */
        $response = $this->postJson("/attendance/{$this->attendance->id}", [
            'year' => $this->attendance->date->format('Y年'),
            'date' => $this->attendance->date->format('m月d日'),
            'clock_in' => '1900',
            'clock_outt' => '1800',
            'remark' => 'TEST',
        ]);

        $response->assertJsonValidationErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 休憩開始時間を退勤時間より後に設定する
        4. 保存処理をする

        「出勤時間もしくは退勤時間が不適切な値です」というバリデーションメッセージが表示されること
    */
    public function test_validates_break_start_not_after_clock_out()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細ページを開き、休憩開始時間を退勤時間より後に設定する */
        $response = $this->postJson("/attendance/{$this->attendance->id}", [
            'date' => $this->attendance->date->format('Y年m月d日'),
            'clock_in' => '0900',
            'clock_out' => '1800',
            'break_times' => [
                ['start' => '1900', 'end' => '2000']
            ],
            'remark' => 'TEST',
        ]);

        /* 「出勤時間もしくは退勤時間が不適切な値です」というバリデーションメッセージが表示されること */
        $response->assertJsonValidationErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 休憩終了時間を退勤時間より後に設定する
        4. 保存処理をする

        「出勤時間もしくは退勤時間が不適切な値です」というバリデーションメッセージが表示されること
    */
    public function test_validates_break_end_not_after_clock_out()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細ページを開き、休憩終了時間を退勤時間より後に設定する */
        $response = $this->postJson("/attendance/{$this->attendance->id}", [
            'date' => $this->attendance->date->format('Y年m月d日'),
            'clock_in' => '0900',
            'clock_out' => '1800',
            'break_times' => [
                ['start' => '1200', 'end' => '1900']
            ],
            'remark' => 'TEST',
        ]);

        /* 「出勤時間もしくは退勤時間が不適切な値です」というバリデーションメッセージが表示されること */
        $response->assertJsonValidationErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 備考欄を未入力のまま保存処理をする

        「備考を記入してください」というバリデーションメッセージが表示されること
    */
    public function test_validates_remark_is_required()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細ページを開き、備考欄を未入力のまま保存処理をする */
        $response = $this->postJson("/attendance/{$this->attendance->id}", [
            'date' => $this->attendance->date->format('Y年m月d日'),
            'clock_in' => '0900',
            'clock_out' => '1800',
            'remark' => '',
        ]);

        /* 「備考を記入してください」というバリデーションメッセージが表示されること */
        $response->assertJsonValidationErrors([
            'remark' => '備考を記入してください'
        ]);
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細を修正し保存処理をする
        3. 管理者ユーザーで承認画面と申請一覧画面を確認する

        修正申請が実行され、管理者の承認画面と申請一覧画面に表示されること
    */
    public function test_correction_request_is_created_and_visible_to_admin()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細を修正し保存処理をする */
        $response = $this->post("/attendance/{$this->attendance->id}", [
            'year' => $this->attendance->date->format('Y年'),
            'date' => $this->attendance->date->format('m月d日'),
            'clock_in' => '09:30',
            'clock_out' => '18:30',
            'remark' => 'TEST',
        ]);
        $correction = AttendanceCorrection::first();

        /* 管理者としてログイン */
        $this->actingAs($this->admin, 'admin');

        /* 管理者の申請一覧画面に表示されること */
        $response = $this->get('/stamp_correction_request/list');
        $response->assertSee($correction->user->name);
        $response->assertSee($correction->date->format('Y/m/d'));

        /* 管理者の承認画面に表示されること */
        $response = $this->get("/stamp_correction_request/approve/{$correction->id}");
        $response->assertOk();
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細を修正し保存処理をする
        3. 申請一覧画面を確認する

        申請一覧に自分の申請が全て表示されていること
    */
    public function test_user_can_see_pending_correction_requests()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細を修正し保存処理をする */
        $response = $this->post("/attendance/{$this->attendance->id}", [
            'year' => $this->attendance->date->format('Y年'),
            'date' => $this->attendance->date->format('m月d日'),
            'clock_in' => '09:30',
            'clock_out' => '18:30',
            'remark' => 'TEST',
        ]);
        $correction = AttendanceCorrection::first();

        /* 申請一覧画面を確認する */
        $response = $this->get('/stamp_correction_request/list');

        /* 申請一覧に自分の申請が全て表示されていること */
        $response->assertSee('承認待ち');
        $response->assertSee($correction->user->name);
        $response->assertSee($correction->date->format('Y/m/d'));
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細を修正し保存処理をする
        3. 申請一覧画面を開く
        4. 管理者が承認した修正申請が全て表示されていることを確認

        承認済みに管理者が承認した申請が全て表示されていること
    */
    public function test_user_can_see_approved_correction_requests()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 承認済み修正申請を作成 */
        $correction = AttendanceCorrection::create([
            'user_id' => $this->user->id,
            'attendance_id' => $this->attendance->id,
            'date' => $this->attendance->date,
            'clock_in_at' => now()->setHour(9),
            'clock_out_at' => now()->setHour(18),
            'remark' => 'TEST',
            'status' => CorrectionStatus::APPROVED,
        ]);

        /* 申請一覧画面を開く */
        $response = $this->get('/stamp_correction_request/list');

        /* 承認済みに管理者が承認した申請が全て表示されていること */
        $response->assertSee('承認済み');
        $response->assertSee($correction->date->format('Y/m/d'));
    }

    /*
        各申請の「詳細」を押下すると申請詳細画面に遷移する
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細を修正し保存処理をする
        3. 申請一覧画面を開く
        4. 「詳細」ボタンを押す

        申請詳細画面に遷移すること
    */
    public function test_user_can_access_correction_request_detail()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細を修正し保存処理をする */
        $response = $this->post("/attendance/{$this->attendance->id}", [
            'year' => $this->attendance->date->format('Y年'),
            'date' => $this->attendance->date->format('m月d日'),
            'clock_in' => '09:30',
            'clock_out' => '18:30',
            'remark' => 'TEST',
        ]);
        $correction = AttendanceCorrection::first();

        /* 申請一覧画面を開き、「詳細」ボタンを押す */
        $response = $this->get("/attendance/{$correction->attendance_id}");

        /*
            申請詳細画面に遷移すること
                →申請詳細画面＝ユーザー側は勤怠詳細画面（承認待ちのケース）となる
        */
        $response->assertOk();
    }
}
