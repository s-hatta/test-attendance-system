<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Admin;
use App\Enums\AttendanceStatus;
use Carbon\Carbon;

class ClockInTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Admin $admin;
    private Carbon $today;

    protected function setUp(): void
    {
        parent::setUp();
        $this->today = Carbon::create(2024, 1, 15, 9, 0, 0);
        Carbon::setTestNow($this->today);
        $this->user = User::factory()->create();
    }

    /*
        1. ステータスが勤務外のユーザーにログインする
        2. 画面に「出勤」ボタンが表示されていることを確認する
        3. 出勤の処理を行う

        画面上に「出勤」ボタンが表示され、処理後に画面上に表示されるステータスが「勤務中」になること
    */
    public function test_clock_in_001(): void
    {
        /* ステータスが勤務外のユーザーにログインする */
        $response = $this->actingAs($this->user, 'user')
            ->get('/attendance');

        /* 画面に「出勤」ボタンが表示されていることを確認する */
        $response->assertSee('出勤');
        $response->assertDontSee('退勤');
        $response->assertDontSee('休憩入');

        /* 出勤の処理を行う */
        $response = $this->post('/attendance/clock-in', [
            'timestamp' => now(),
        ]);

        /*  画面上に「出勤」ボタンが表示され、処理後に画面上に表示されるステータスが「勤務中」になること */
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
        $response->assertDontSee('勤務外');
        $this->assertDatabaseHas('attendances', [
            'user_id' => $this->user->id,
            'date' => $this->today->format('Y-m-d'),
            'status' => AttendanceStatus::WORKING->value,
        ]);
    }

    /*
        1. ステータスが退勤済であるユーザーにログインする
        2. 勤務ボタンが表示されないことを確認する

        画面上に「出勤」ボタンが表示されないこと
    */
    public function test_clock_in_002(): void
    {
        /* ステータスが退勤済であるユーザーにログインする */
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => $this->today->format('Y-m-d'),
            'clock_in_at' => $this->today->copy()->subHours(8),
            'clock_out_at' => $this->today,
            'status' => AttendanceStatus::LEFT->value,
        ]);
        $response = $this->actingAs($this->user, 'user')
            ->get('/attendance');

        /* 画面上に「出勤」ボタンが表示されないこと */
        $response->assertDontSee('出勤');
        $response->assertSee('お疲れさまでした');
    }

    /*
        1. ステータスが勤務外のユーザーにログインする
        2. 出勤の処理を行う
        3. 管理画面から出勤の日付を確認する

        管理画面に出勤時刻が正確に記録されていること
    */
    public function test_clock_in_003(): void
    {
        /* ステータスが勤務外のユーザーにログインし、出勤の処理を行う */
        $this->actingAs($this->user, 'user')
            ->post('/attendance/clock-in', [
                'timestamp' => $this->today,


            ]);

        /* 管理画面から出勤の日付を確認する */
        $response = $this->actingAs($this->user, 'user')
            ->get('/attendance/list');

        /* 管理画面に出勤時刻が正確に記録されていること */
        $response->assertSee($this->today->format('H:i'));
        $attendance = Attendance::where('user_id', $this->user->id)
            ->where('date', $this->today->format('Y-m-d'))
            ->first();
        $this->assertNotNull($attendance);
        $this->assertEquals(
            $this->today->format('Y-m-d H:i:00'),
            $attendance->clock_in_at->format('Y-m-d H:i:00')
        );
    }
}
