<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Enums\AttendanceStatus;
use Carbon\Carbon;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;
    private User $user;
    private Attendance $attendance;
    private BreakTime $breakTime;

    protected function setUp(): void
    {
        parent::setUp();

        /* 管理者作成 */
        $this->admin = Admin::create([
            'name' => 'test_admin',
            'email' => 'test@admin.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        /* テストユーザー作成 */
        $this->user = User::factory()->create();

        /* 勤怠データ作成 */
        $now = Carbon::now();
        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $now->format('Y-m-d'),
            'clock_in_at' => $now->copy()->setHour(9),
            'clock_out_at' => $now->copy()->setHour(18),
            'status' => AttendanceStatus::LEFT,
        ]);

        /* 休憩データ作成 +*/
        $this->breakTime = BreakTime::create([
            'attendance_id' => $this->attendance->id,
            'start_at' => $now->copy()->setHour(12),
            'end_at' => $now->copy()->setHour(13),
        ]);
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 勤怠詳細ページを開く

        詳細画面の内容が選択した情報と一致すること
    */
    public function test_admin_can_see_attendance_details()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠詳細ページを開く */
        $response = $this->get("/admin/attendance/{$this->attendance->id}");

        /* 詳細画面の内容が選択した情報と一致すること */
        $response->assertOk()
                ->assertSee($this->user->name)
                ->assertSee($this->attendance->date->format('Y'))
                ->assertSee($this->attendance->date->format('md'))
                ->assertSee($this->attendance->clock_in_at->format('Hi'))
                ->assertSee($this->attendance->clock_out_at->format('Hi'))
                ->assertSee($this->breakTime->start_at->format('Hi'))
                ->assertSee($this->breakTime->end_at->format('Hi'));
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 出勤時間を退勤時間より後に設定する
        4. 保存処理をする

        「出勤時間もしくは退勤時間が不適切な値です」というバリデーションメッセージが表示されること
    */
    public function test_validates_clock_in_not_after_clock_out()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠詳細ページを開き、出勤時間を退勤時間より後に設定する */
        $response = $this->postJson("/admin/attendance/{$this->attendance->id}", [
            'date' => $this->attendance->date,
            'clock_in_at' => '1900',
            'clock_out_at' => '1800',
            'remark' => 'TEST',
        ]);

        /* 「出勤時間もしくは退勤時間が不適切な値です」というバリデーションメッセージが表示されること */
        $response->assertJsonValidationErrors([
            'clock_in' => '出勤時間もしくは退勤時間が不適切な値です',
            'clock_out' => '出勤時間もしくは退勤時間が不適切な値です'
        ]);
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 休憩開始時間を退勤時間より後に設定する
        4. 保存処理をする

        「出勤時間もしくは退勤時間が不適切な値です」というバリデーションメッセージが表示されること
    */
    public function test_validates_break_start_not_after_clock_out()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠詳細ページを開き、休憩開始時間を退勤時間より後に設定する */
        $response = $this->postJson("/admin/attendance/{$this->attendance->id}", [
            'date' => $this->attendance->date,
            'clock_in_at' => '0900',
            'clock_out_at' => '1800',
            'break_times' => [
                ['start_at' => '1900', 'end_at' => '2000']
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
        1. 管理者ユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 休憩終了時間を退勤時間より後に設定する
        4. 保存処理をする

        「出勤時間もしくは退勤時間が不適切な値です」というバリデーションメッセージが表示されること
    */
    public function test_validates_break_end_not_after_clock_out()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠詳細ページを開き、休憩終了時間を退勤時間より後に設定する */
        $response = $this->postJson("/admin/attendance/{$this->attendance->id}", [
            'date' => $this->attendance->date,
            'clock_in_at' => '0900',
            'clock_out_at' => '1800',
            'break_times' => [
                ['start_at' => '1200', 'end_at' => '1900']
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
        1. 管理者ユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 備考欄を未入力のまま保存処理をする

        「備考を記入してください」というバリデーションメッセージが表示されること
    */
    public function test_validates_remark_is_required()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠詳細ページを開き、備考欄を未入力のまま保存処理をする */
        $response = $this->postJson("/admin/attendance/{$this->attendance->id}", [
            'date' => $this->attendance->date,
            'clock_in_at' => '0900',
            'clock_out_at' => '1800',
            'remark' => '',
        ]);

        /* 「備考を記入してください」というバリデーションメッセージが表示されること */
        $response->assertJsonValidationErrors([
            'remark' => '備考を記入してください'
        ]);
    }
}
