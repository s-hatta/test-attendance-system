<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Enums\AttendanceStatus;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $users;
    protected $attendances;

    protected function setUp(): void
    {
        parent::setUp();

        /* テストユーザー作成 */
        $this->users = User::factory()->count(3)->create();

        /* 管理者作成 */
        $this->admin = Admin::create([
            'name' => 'test_admin',
            'email' => 'test@admin.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $this->createAttendanceData();
    }

    private function createAttendanceData(): void
    {
        $now = Carbon::now();

        foreach ($this->users as $user) {
            /* 今日のデータ */
            Attendance::create([
                'user_id' => $user->id,
                'date' => $now->format('Y-m-d'),
                'clock_in_at' => $now->copy()->setHour(9)->setMinute($user->id),
                'clock_out_at' => $now->copy()->setHour(18)->setMinute($user->id),
                'status' => AttendanceStatus::LEFT,
            ]);

            /* 前日のデータ */
            Attendance::create([
                'user_id' => $user->id,
                'date' => $now->copy()->subDay()->format('Y-m-d'),
                'clock_in_at' => $now->copy()->subDay()->setHour(9)->setMinute($user->id),
                'clock_out_at' => $now->copy()->subDay()->setHour(18)->setMinute($user->id),
                'status' => AttendanceStatus::LEFT,
            ]);

            // 翌日のデータ
            Attendance::create([
                'user_id' => $user->id,
                'date' => $now->copy()->addDay()->format('Y-m-d'),
                'clock_in_at' => $now->copy()->addDay()->setHour(9)->setMinute($user->id),
                'clock_out_at' => $now->copy()->addDay()->setHour(18)->setMinute($user->id),
                'status' => AttendanceStatus::LEFT,
            ]);
        }
    }

    /*
        1. 管理者ユーザーにログインする
        2. 勤怠一覧画面を開く

        その日の全ユーザーの勤怠情報が正確な値になっていること
    */
    public function test_admin_can_see_all_users_attendance_for_current_day()
    {
        /* 管理者ユーザーにログインする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠一覧画面を開く */
        $response = $this->get('/admin/attendance/list');

        /* その日の全ユーザーの勤怠情報が正確な値になっていること */
        $response->assertOk();
        foreach ($this->users as $user) {
            $clockInTime = sprintf('09:%02d', $user->id);
            $clockOutTime = sprintf('18:%02d', $user->id);
            $response->assertSee($user->name);
            $response->assertSee($clockInTime);
            $response->assertSee($clockOutTime);
        }
    }

    /*
        1. 管理者ユーザーにログインする
        2. 勤怠一覧画面を開く

        勤怠一覧画面にその日の日付が表示されていること
    */
    public function test_current_date_is_displayed_by_default()
    {
        /* 管理者ユーザーにログインする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠一覧画面を開く */
        $response = $this->get('/admin/attendance/list');

        /* 勤怠一覧画面にその日の日付が表示されていること */
        $response->assertOk();
        $response->assertSee(Carbon::now()->format('Y/m/d'));
    }

    /*
        1. 管理者ユーザーにログインする
        2. 勤怠一覧画面を開く
        3. 「前日」ボタンを押す

        前日の日付の勤怠情報が表示されること
    */
    public function test_admin_can_navigate_to_previous_day()
    {
        /* 管理者ユーザーにログインする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠一覧画面を開き、「前日」ボタンを押す */
        $prevDay = Carbon::now()->subDay();
        $response = $this->get(
            '/admin/attendance/list?' .
            'year='. $prevDay->year .
            '&month='. $prevDay->month .
            '&day='. $prevDay->day
        );

        /* 前日の日付の勤怠情報が表示されること */
        $response->assertOk();
        $response->assertSee($prevDay->format('Y/m/d'));
        foreach ($this->users as $user) {
            $clockInTime = sprintf('09:%02d', $user->id);
            $clockOutTime = sprintf('18:%02d', $user->id);
            $response->assertSee($user->name);
            $response->assertSee($clockInTime);
            $response->assertSee($clockOutTime);
        }
    }

    /*
        1. 管理者ユーザーにログインする
        2. 勤怠一覧画面を開く
        3. 「翌日」ボタンを押す

        翌日の日付の勤怠情報が表示されること
    */
    public function test_admin_can_navigate_to_next_day()
    {
        /* 管理者ユーザーにログインする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠一覧画面を開き、「翌日」ボタンを押す */
        $nextDay = Carbon::now()->addDay();
        $response = $this->get(
            '/admin/attendance/list?' .
            'year='. $nextDay->year .
            '&month='. $nextDay->month .
            '&day='. $nextDay->day
        );

        /* 翌日の日付の勤怠情報が表示されること */
        $response->assertOk();
        $response->assertSee($nextDay->format('Y/m/d'));
        foreach ($this->users as $user) {
            $clockInTime = sprintf('09:%02d', $user->id);
            $clockOutTime = sprintf('18:%02d', $user->id);
            $response->assertSee($user->name);
            $response->assertSee($clockInTime);
            $response->assertSee($clockOutTime);
        }
    }
}
