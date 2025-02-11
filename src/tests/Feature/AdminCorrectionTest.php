<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\BreakTime;
use App\Models\BreakTimeCorrection;
use App\Enums\AttendanceStatus;
use App\Enums\CorrectionStatus;
use Carbon\Carbon;

class AdminCorrectionTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $users;
    protected $corrections;

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
        $this->users = User::factory()->count(2)->create();

        $this->setupCorrectionData();
    }

    private function setupCorrectionData(): void
    {
        $now = Carbon::now();

        foreach( $this->users as $user ) {
            /* 通常の勤怠データ */
            $attendance = Attendance::create([
                'user_id' => $user->id,
                'date' => $now->format('Y-m-d'),
                'clock_in_at' => $now->copy()->setHour(9)->setMinute($user->id),
                'clock_out_at' => $now->copy()->setHour(18)->setMinute($user->id),
                'status' => AttendanceStatus::LEFT,
            ]);

            /* 承認待ちの修正申請 */
            AttendanceCorrection::create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'date' => $now->format('Y-m-d'),
                'clock_in_at' => $now->copy()->setHour(8)->setMinute($user->id+10),
                'clock_out_at' => $now->copy()->setHour(17)->setMinute($user->id+10),
                'remark' => '承認待ち',
                'status' => CorrectionStatus::PENDING,
            ]);

            /* 承認済みの修正申請 */
            AttendanceCorrection::create([
                'user_id' => $user->id,
                'attendance_id' => $attendance->id,
                'date' => $now->format('Y-m-d'),
                'clock_in_at' => $now->copy()->setHour(10)->setMinute($user->id+20),
                'clock_out_at' => $now->copy()->setHour(19)->setMinute($user->id+20),
                'remark' => '承認済み',
                'status' => CorrectionStatus::APPROVED,
            ]);
            $now = $now->addDay();
        }
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 修正申請一覧ページを開き、承認待ちのタブを開く

        全ユーザーの未承認の修正申請が表示されること
    */
    public function test_admin_can_see_all_pending_correction_requests()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 修正申請一覧ページを開き、承認待ちのタブを開く */
        $response = $this->get('/stamp_correction_request/list');
        $response->assertOk();

        /* 全ユーザーの未承認の修正申請が表示されること */
        foreach( $this->users as $user ) {
            $pendingCorrection = AttendanceCorrection::where('user_id', $user->id)
                ->where('status', CorrectionStatus::PENDING)
                ->first();

            $response->assertSee($user->name)
                ->assertSee($pendingCorrection->date->format('Y/m/d'))
                ->assertSee('承認待ち');
        }
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 修正申請一覧ページを開き、承認済みのタブを開く

        全ユーザーの承認済みの修正申請が表示されること
    */
    public function test_admin_can_see_all_approved_correction_requests()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 修正申請一覧ページを開き、承認済みのタブを開く */
        $response = $this->get('/stamp_correction_request/list');
        $response->assertOk();

        /* 全ユーザーの承認済みの修正申請が表示されること */
        foreach( $this->users as $user ) {
            $approvedCorrection = AttendanceCorrection::where('user_id', $user->id)
                                                    ->where('status', CorrectionStatus::APPROVED)
                                                    ->first();

            $response->assertSee($user->name)
                    ->assertSee($approvedCorrection->date->format('Y/m/d'))
                    ->assertSee('承認済み');
        }
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 修正申請の詳細画面を開く

        申請内容が正しく表示されていること
    */
    public function test_admin_can_see_correction_request_details()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 修正申請の詳細画面を開く */
        $correction = AttendanceCorrection::where('status', CorrectionStatus::PENDING)->first();

        /* 申請内容が正しく表示されていること */
        $response = $this->get("/stamp_correction_request/approve/{$correction->id}");
        $response->assertOk();
        $response->assertSee($correction->user->name);
        $response->assertSee($correction->date->format('Y年'));
        $response->assertSee($correction->date->format('n月j日'));
        $response->assertSee($correction->clock_in_at->format('H:i'));
        $response->assertSee($correction->clock_out_at->format('H:i'));
        $response->assertSee($correction->remark);
    }

    /*
        1. 管理者ユーザーにログインをする
        2. 修正申請の詳細画面で「承認」ボタンを押す

        修正申請が承認され、勤怠情報が更新されること
    */
    public function test_admin_can_approve_correction_request()
    {
        /* 管理者ユーザーにログインをする */
        $this->actingAs($this->admin, 'admin');

        /* 修正申請の詳細画面で「承認」ボタンを押す */
        $correction = AttendanceCorrection::where('status', CorrectionStatus::PENDING)->first();
        $attendance = $correction->attendance;
        $response = $this->post("/stamp_correction_request/approve/{$correction->id}", [
            'status' => CorrectionStatus::APPROVED->value
        ]);

        // 修正申請のステータスが更新されていることを確認
        $this->assertDatabaseHas('attendance_corrections', [
            'id' => $correction->id,
            'status' => CorrectionStatus::APPROVED->value
        ]);

        /* 修正申請が承認され、勤怠情報が更新されること */
        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_in_at' => $correction->clock_in_at,
            'clock_out_at' => $correction->clock_out_at
        ]);
    }
}
