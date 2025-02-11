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

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $attendance;
    protected $breakTime;

    protected function setUp(): void
    {
        parent::setUp();

        /* テストユーザーを作成 */
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email_verified_at' => now(),
        ]);

        /* 勤怠データを作成 */
        $now = Carbon::now();
        $this->attendance = Attendance::create([
            'user_id' => $this->user->id,
            'date' => $now->format('Y-m-d'),
            'clock_in_at' => $now->copy()->setHour(10)->setMinute(11),
            'clock_out_at' => $now->copy()->setHour(18)->setMinute(19),
            'status' => AttendanceStatus::LEFT,
        ]);

        /* 休憩データを作成 */
        $this->breakTime = BreakTime::create([
            'attendance_id' => $this->attendance->id,
            'start_at' => $now->copy()->setHour(12)->setMinute(13),
            'end_at' => $now->copy()->setHour(14)->setMinute(15),
        ]);
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 名前欄を確認する

        名前がログインユーザーの名前になっていること
    */
    public function test_detail_page_shows_correct_user_name()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細ページを開く */
        $response = $this->get('/attendance/' . $this->attendance->id);

        /* 名前がログインユーザーの名前になっていること */
        $response->assertOk();
        $response->assertSee($this->user->name);
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 日付欄を確認する

        日付が選択した日付になっていること
    */
    public function test_detail_page_shows_correct_date()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細ページを開く */
        $response = $this->get('/attendance/' . $this->attendance->id);

        /* 日付が選択した日付になっていること */
        $response->assertOk();
        $response->assertSee($this->attendance->date->format('Y'));
        $response->assertSee($this->attendance->date->format('m'));
        $response->assertSee($this->attendance->date->format('d'));
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 出勤・退勤欄を確認する

        「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致していること
    */
    public function test_detail_page_shows_correct_clock_times()
    {
        /* 勤怠情報が登録されたユーザーにログインをする */
        $this->actingAs($this->user);

        /* 勤怠詳細ページを開く */
        $response = $this->get('/attendance/' . $this->attendance->id);

        /* 「出勤・退勤」にて記されている時間がログインユーザーの打刻と一致していること */
        $response->assertOk();
        $response->assertSee($this->attendance->clock_in_at->format('Hi'));
        $response->assertSee($this->attendance->clock_out_at->format('Hi'));
    }

    /*
        1. 勤怠情報が登録されたユーザーにログインをする
        2. 勤怠詳細ページを開く
        3. 休憩欄を確認する

        「休憩」にて記されている時間がログインユーザーの打刻と一致していること
    */
    public function test_detail_page_shows_correct_break_times()
    {
        $this->actingAs($this->user);

        $response = $this->get('/attendance/' . $this->attendance->id);

        $response->assertOk();
        $response->assertSee($this->breakTime->start_at->format('Hi'));
        $response->assertSee($this->breakTime->end_at->format('Hi'));
    }
}
