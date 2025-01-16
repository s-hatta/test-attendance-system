<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Enums\AttendanceStatus;
use Carbon\Carbon;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }
    
    /*
        1. ステータスが勤務外のユーザーにログインする
        2. 勤怠打刻画面を開く
        3. 画面に表示されているステータスを確認する
        
        画面上に表示されているステータスが「勤務外」となること
    */
    public function test_attendance_status_off_duty(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::now(),
            'status' => AttendanceStatus::OFF_DUTY->value,
        ]);
        $response = $this->actingAs($this->user)->get('/attendance');
        $response->assertSee('勤務外');
    }
    
    /*
        1. ステータスが勤務中のユーザーにログインする
        2. 勤怠打刻画面を開く
        3. 画面に表示されているステータスを確認する
        
        画面上に表示されているステータスが「勤務中」となること
    */
    public function test_attendance_status_working(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::now(),
            'status' => AttendanceStatus::WORKING->value,
        ]);
        $response = $this->actingAs($this->user)->get('/attendance');
        $response->assertSee('出勤中');
    }
    
    /*
        1. ステータスが休憩中のユーザーにログインする
        2. 勤怠打刻画面を開く
        3. 画面に表示されているステータスを確認する
        
        画面上に表示されているステータスが「休憩中」となること
    */
    public function test_attendance_status_break(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::now(),
            'status' => AttendanceStatus::BREAK->value,
        ]);
        $response = $this->actingAs($this->user)->get('/attendance');
        $response->assertSee('休憩中');
    }
    
    /*
        1. ステータスが退勤済のユーザーにログインする
        2. 勤怠打刻画面を開く
        3. 画面に表示されているステータスを確認する
        
        画面上に表示されているステータスが「退勤済」となること
    */
    public function test_attendance_status_left(): void
    {
        Attendance::create([
            'user_id' => $this->user->id,
            'date' => Carbon::now(),
            'status' => AttendanceStatus::LEFT->value,
        ]);
        $response = $this->actingAs($this->user)->get('/attendance');
        $response->assertSee('退勤済');
    }
}
