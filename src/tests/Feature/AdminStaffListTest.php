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

class AdminStaffListTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $users;
    protected $attendances;

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
        $this->users = collect([
            User::factory()->create([
                'name' => 'Test User 1',
                'email' => 'user1@example.com',
            ]),
            User::factory()->create([
                'name' => 'Test User 2',
                'email' => 'user2@example.com',
            ]),
        ]);

        $this->createAttendanceData();
    }

    private function createAttendanceData(): void
    {
        $now = Carbon::now();

        foreach ($this->users as $user) {
            /* 現在月のデータ */
            Attendance::create([
                'user_id' => $user->id,
                'date' => $now->format('Y-m-d'),
                'clock_in_at' => $now->copy()->setHour(9)->setMinute($user->id),
                'clock_out_at' => $now->copy()->setHour(18)->setMinute($user->id),
                'status' => AttendanceStatus::LEFT,
            ]);

            /* 前月のデータ */
            Attendance::create([
                'user_id' => $user->id,
                'date' => $now->copy()->subMonth()->format('Y-m-d'),
                'clock_in_at' => $now->copy()->setHour(9)->setMinute($user->id+10),
                'clock_out_at' => $now->copy()->setHour(18)->setMinute($user->id+10),
                'status' => AttendanceStatus::LEFT,
            ]);

            /* 翌月のデータ */
            Attendance::create([
                'user_id' => $user->id,
                'date' => $now->copy()->addMonth()->format('Y-m-d'),
                'clock_in_at' => $now->copy()->setHour(9)->setMinute($user->id+20),
                'clock_out_at' => $now->copy()->setHour(18)->setMinute($user->id+20),
                'status' => AttendanceStatus::LEFT,
            ]);
        }
    }

    /*
        1. 管理者でログインする
        2. スタッフ一覧ページを開く

        全ての一般ユーザーの氏名とメールアドレスが正しく表示されていること
    */
    public function test_admin_can_see_all_users_information()
    {
        /* 管理者でログインする */
        $this->actingAs($this->admin, 'admin');

        /* スタッフ一覧ページを開く */
        $response = $this->get('/admin/staff/list');

        /* 全ての一般ユーザーの氏名とメールアドレスが正しく表示されていること */
        $response->assertOk();
        foreach ($this->users as $user) {
            $response->assertSee($user->name)
                    ->assertSee($user->email);
        }
    }

    /*
        1. 管理者ユーザーでログインする
        2. 選択したユーザーの勤怠一覧ページを開く

        勤怠情報が正確に表示されること
    */
    public function test_admin_can_see_user_attendance_records()
    {
        /* 管理者ユーザーでログインする */
        $this->actingAs($this->admin, 'admin');

        /* 選択したユーザーの勤怠一覧ページを開く */
        $user = $this->users->first();
        $response = $this->get("/admin/attendance/staff/{$user->id}");

        /* 勤怠情報が正確に表示されること */
        $clockInTime = sprintf('09:%02d', $user->id);
        $clockOutTime = sprintf('18:%02d', $user->id);
        $response->assertOk();
        $response->assertSee($clockInTime);
        $response->assertSee($clockOutTime);
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 勤怠一覧ページを開く
        3. 「前月」ボタンを押す

        前月の情報が表示されていること
    */
    public function test_admin_can_navigate_to_previous_month()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠一覧ページを開き、「前月」ボタンを押す */
        $user = $this->users->first();
        $prevMonth = Carbon::now()->subMonth();
        $response = $this->get("/admin/attendance/staff/{$user->id}?year={$prevMonth->year}&month={$prevMonth->month}");

        /* 前月の情報が表示されていること */
        $clockInTime = sprintf('09:%02d', $user->id+10);
        $clockOutTime = sprintf('18:%02d', $user->id+10);
        $response->assertOk();
        $response->assertSee($prevMonth->format('Y/m'));
        $response->assertSee($clockInTime);
        $response->assertSee($clockOutTime);
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 勤怠一覧ページを開く
        3. 「翌月」ボタンを押す

        翌月の情報が表示されていること
    */
    public function test_admin_can_navigate_to_next_month()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠一覧ページを開き、「翌月」ボタンを押す */
        $user = $this->users->first();
        $nextMonth = Carbon::now()->addMonth();
        $response = $this->get("/admin/attendance/staff/{$user->id}?year={$nextMonth->year}&month={$nextMonth->month}");

        /* 翌月の情報が表示されていること */
        $clockInTime = sprintf('09:%02d', $user->id+20);
        $clockOutTime = sprintf('18:%02d', $user->id+20);
        $response->assertOk();
        $response->assertSee($nextMonth->format('Y/m'));
        $response->assertSee($clockInTime);
        $response->assertSee($clockOutTime);
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 勤怠一覧ページを開く
        3. 「詳細」ボタンを押下する

        その日の勤怠詳細画面に遷移すること
    */
    public function test_admin_can_access_attendance_detail()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 勤怠一覧ページを開く */
        $user = $this->users->first();
        $attendance = Attendance::where('user_id', $user->id)
            ->where('date', Carbon::now()->format('Y-m-d'))
            ->first();

        /* 「詳細」ボタンを押下する */
        $response = $this->get("/admin/attendance/{$attendance->id}");

        /* その日の勤怠詳細画面に遷移すること */
        $response->assertOk();
        $response->assertViewIs('admin.attendance.show');
    }
}
