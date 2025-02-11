<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Enums\AttendanceStatus;
use Carbon\Carbon;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $attendances;

    protected function setUp(): void
    {
        parent::setUp();

        /* ユーザーを作成 */
        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        /* 当月、前月、翌月の勤怠データを作成 */
        $this->createAttendanceData();
    }

    private function createAttendanceData(): void
    {
        $now = Carbon::now();

        /* 当月のデータ */
        $this->attendances['current'] = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $now->format('Y-m-d'),
            'clock_in_at' => $now->copy()->setHour(1),
            'clock_out_at' => $now->copy()->setHour(2),
            'status' => AttendanceStatus::LEFT,
        ]);

        /* 前月のデータ */
        $this->attendances['prev'] = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $now->copy()->subMonth()->format('Y-m-d'),
            'clock_in_at' => $now->copy()->subMonth()->setHour(3),
            'clock_out_at' => $now->copy()->subMonth()->setHour(4),
            'status' => AttendanceStatus::LEFT,
        ]);

        /* 翌月のデータ */
        $this->attendances['next'] = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $now->copy()->addMonth()->format('Y-m-d'),
            'clock_in_at' => $now->copy()->addMonth()->setHour(5),
            'clock_out_at' => $now->copy()->addMonth()->setHour(6),
            'status' => AttendanceStatus::LEFT,
        ]);
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインする
        2. 勤怠一覧ページを開く
        3. 自分の勤怠情報がすべて表示されていることを確認する

        自分の勤怠情報が全て表示されていること
    */
    public function test_user_can_see_all_attendance_records()
    {
        /* 勤怠情報が登録されたユーザーにログインする */
        $this->actingAs($this->user);

        /* 勤怠一覧ページを開く */
        $response = $this->get('/attendance/list');

        /* 自分の勤怠情報が全て表示されていること */
        $response->assertOk();
        $response->assertSee($this->attendances['current']->clock_in_at->format('H:i'));
        $response->assertSee($this->attendances['current']->clock_out_at->format('H:i'));
        $response->assertDontSee($this->attendances['prev']->clock_in_at->format('H:i'));
        $response->assertDontSee($this->attendances['prev']->clock_out_at->format('H:i'));
        $response->assertDontSee($this->attendances['next']->clock_in_at->format('H:i'));
        $response->assertDontSee($this->attendances['next']->clock_out_at->format('H:i'));
    }

    /*
        1. ユーザーにログインをする
        2. 勤怠一覧ページを開く

        現在の月が表示されていること
    */
    public function test_current_month_is_displayed_by_default()
    {
        $this->actingAs($this->user);

        $response = $this->get('/attendance/list');

        $response->assertOk();
        $response->assertSee(Carbon::now()->format('Y/m'));
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠一覧ページを開く
        3. 「前月」ボタンを押す

        前月の情報が表示されていること
    */
    public function test_user_can_navigate_to_previous_month()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠一覧ページを開き、「前月」ボタンを押す */
        $prevMonth = Carbon::now()->subMonth();
        $response = $this->get('/attendance/list?year=' . $prevMonth->year . '&month=' . $prevMonth->month);

        /* 前月の情報が表示されていること */
        $response->assertOk();
        $response->assertSee($prevMonth->format('Y/m'));
        $response->assertDontSee($this->attendances['current']->clock_in_at->format('H:i'));
        $response->assertDontSee($this->attendances['current']->clock_out_at->format('H:i'));
        $response->assertSee($this->attendances['prev']->clock_in_at->format('H:i'));
        $response->assertSee($this->attendances['prev']->clock_out_at->format('H:i'));
        $response->assertDontSee($this->attendances['next']->clock_in_at->format('H:i'));
        $response->assertDontSee($this->attendances['next']->clock_out_at->format('H:i'));
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠一覧ページを開く
        3. 「翌月」ボタンを押す

        翌月の情報が表示されていること
    */
    public function test_user_can_navigate_to_next_month()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠一覧ページを開き、「翌月」ボタンを押す */
        $nextMonth = Carbon::now()->addMonth();
        $response = $this->get('/attendance/list?year=' . $nextMonth->year . '&month=' . $nextMonth->month);

        /* 翌月の情報が表示されていること */
        $response->assertOk();
        $response->assertSee($nextMonth->format('Y/m'));
        $response->assertDontSee($this->attendances['current']->clock_in_at->format('H:i'));
        $response->assertDontSee($this->attendances['current']->clock_out_at->format('H:i'));
        $response->assertDontSee($this->attendances['prev']->clock_in_at->format('H:i'));
        $response->assertDontSee($this->attendances['prev']->clock_out_at->format('H:i'));
        $response->assertSee($this->attendances['next']->clock_in_at->format('H:i'));
        $response->assertSee($this->attendances['next']->clock_out_at->format('H:i'));
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠一覧ページを開く
        3. 「詳細」ボタンを押下する

        その日の勤怠詳細画面に遷移すること
    */
    public function test_user_can_access_attendance_detail()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠一覧ページを開き、「詳細」ボタンを押下する */
        $response = $this->get('/attendance/' . $this->attendances['current']->id);

        /* その日の勤怠詳細画面に遷移すること */
        $response->assertOk();
        $response->assertViewIs('user.attendance.show');
    }
}
