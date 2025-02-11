<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Enums\AttendanceStatus;
use Carbon\Carbon;

class BreakTimeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Attendance $attendance;

    protected function setUp(): void
    {
        parent::setUp();

        /* テストユーザーを作成 */
        $this->user = User::factory()->create();

        /* 勤務中の出勤データを作成 */
        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => now()->toDateString(),
            'clock_in_at' => now()->subHours(2),
            'status' => AttendanceStatus::WORKING,
        ]);
    }

    /*
        1. ステータスが勤務中のユーザーにログインする
        2. 画面に「休憩入」ボタンが表示されていることを確認する
        3. 休憩の処理を行う

        画面上に「休憩入」ボタンが表示され、処理後に画面上に表示されるステータスが「休憩中」になること
    */
    public function test_working_user_can_start_break()
    {
        /* ステータスが勤務中のユーザーにログインする */
        $response = $this->actingAs($this->user)
            ->get('/attendance');

        /* 画面に「休憩入」ボタンが表示されていることを確認する */
        $response->assertSee('休憩入');

        /* 休憩の処理を行う */
        $timestamp = now();
        $response = $this->post('/attendance/break-start', [
            'timestamp' => $timestamp,
        ]);

        /* 画面上に「休憩入」ボタンが表示され、処理後に画面上に表示されるステータスが「休憩中」になること */
        $response = $this->get('/attendance');
        $response->assertSee('休憩中');
    }

    /*
        1. ステータスが出勤中であるユーザーにログインする
        2. 休憩入と休憩戻の処理を行う
        3. 「休憩入」ボタンが表示されることを確認する

        画面上に「休憩入」ボタンが表示されること
    */
    public function test_user_can_end_break_and_start_new_break()
    {
        /* ステータスが出勤中であるユーザーにログインする */
        $this->actingAs($this->user);

        /* 休憩入と休憩戻の処理を行う */
        $startTime = now();
        $this->post('/attendance/break-start', ['timestamp' => $startTime]);
        $endTime = now()->addMinutes(30);
        $this->post('/attendance/break-end', ['timestamp' => $endTime]);

        /* 画面上に「休憩入」ボタンが表示されること */
        $response = $this->get('/attendance');
        $response->assertSee('休憩入');
    }

    /*
        1. ステータスが出勤中であるユーザーにログインする
        2. 休憩入の処理を行う
        3. 休憩戻の処理を行う

        休憩戻ボタンが表示され、処理後にステータスが「出勤中」に変更されること
    */
    public function test_user_can_end_break_and_status_changes_to_working()
    {
        /* ステータスが出勤中であるユーザーにログインする */
        $this->actingAs($this->user);

        /* 休憩入の処理を行う */
        $this->post('/attendance/break-start', ['timestamp' => now()]);
        $response = $this->get('/attendance');

        /* 休憩戻ボタンが表示されること */
        $response->assertSee('休憩戻');

        /* 休憩戻の処理を行う */
        $this->post('/attendance/break-end', ['timestamp' => now()->addMinutes(30)]);

        /* ステータスが「出勤中」に変更されること */
        $response = $this->get('/attendance');
        $response->assertSee('出勤中');
    }

    /*
    1. ステータスが出勤中であるユーザーにログインする
    2. 休憩入と休憩戻の処理を行い、再度休憩入の処理を行う
    3. 「休憩戻」ボタンが表示されることを確認する

    画面上に「休憩戻」ボタンが表示されること
    */
    public function test_user_can_start_multiple_breaks()
    {
        /* ステータスが出勤中であるユーザーにログインする */
        $this->actingAs($this->user);

        /* 1回目の休憩・休憩戻 */
        $this->post('/attendance/break-start', ['timestamp' => now()]);
        $this->post('/attendance/break-end', ['timestamp' => now()->addMinutes(30)]);

        /* 2回目の休憩開始 */
        $this->post('/attendance/break-start', ['timestamp' => now()->addMinutes(60)]);

        /* 「休憩戻」ボタンが表示されることを確認する */
        $response = $this->get('/attendance');
        $response->assertSee('休憩戻');
    }

    /*
        1. ステータスが勤務中のユーザーにログインする
        2. 休憩入と休憩戻の処理を行う
        3. 管理画面から休憩の日付を確認する

        管理画面に休憩時刻が正確に記録されていること
    */
    public function test_break_time_is_correctly_recorded_in_admin_view()
    {
        /* ステータスが勤務中のユーザーにログインする */
        $this->actingAs($this->user);

        /* 休憩入と休憩戻の処理を行う */
        $this->post('/attendance/break-start', ['timestamp' => now()]);
        $this->post('/attendance/break-end', ['timestamp' => now()->addMinutes(30)]);

        /* 管理画面に休憩時刻が正確に記録されていること */
        $response = $this->get('/attendance/list');
        $response->assertSee('0:30');
    }
}
