<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Enums\AttendanceStatus;
use Carbon\Carbon;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /*
        1. ステータスが勤務中のユーザーにログインする
        2. 画面に「退勤」ボタンが表示されていることを確認する
        3. 退勤の処理を行う

        画面上に「退勤」ボタンが表示され、処理後に画面上に表示されるステータスが「退勤済」になること
    */
    public function test_working_user_can_clock_out()
    {
        /* ステータスが勤務中のユーザーにログイン */
        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->toDateString(),
            'clock_in_at' => now()->subHours(2),
            'status' => AttendanceStatus::WORKING,
        ]);
        $this->actingAs($this->user);

        /* 画面に「退勤」ボタンが表示されていることを確認 */
        $response = $this->get('/attendance');
        $response->assertSee('退勤');

        /* 退勤の処理を行う */
        $clockOutTime = now();
        $response = $this->post('/attendance/clock-end', [
            'timestamp' => $clockOutTime,
        ]);

        /* 画面上に「退勤」ボタンが表示され、処理後に画面上に表示されるステータスが「退勤済」になっていることを確認 */
        $response = $this->get('/attendance');
        $response->assertSee('退勤済');
    }

    /*
        1. ステータスが勤務外のユーザーにログインする
        2. 出勤と退勤の処理を行う
        3. 管理画面から退勤の日付を確認する

        管理画面に退勤時刻が正確に記録されていること
    */
    public function test_clock_out_time_is_correctly_recorded()
    {
        /* ステータスが勤務外のユーザーにログインする */
        $this->actingAs($this->user);
        $response = $this->get('/attendance');
        $response->assertSee('出勤');

        /* 出勤と退勤の処理を行う */
        $response = $this->get('/attendance');
        $this->post('/attendance/clock-in', [
            'timestamp' => now()->subHours(2),
        ]);
        $response = $this->get('/attendance');
        $clockOutTime = now()->subHour();
        $this->post('/attendance/clock-end', [
            'timestamp' => $clockOutTime,
        ]);

        /* 管理画面から退勤の日付を確認する */
        $response = $this->get('/attendance/list');

        /* 管理画面に退勤時刻が正確に記録されていること */
        $response->assertSee($clockOutTime->format('H:i'));
    }
}
