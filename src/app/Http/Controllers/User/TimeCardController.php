<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;


class TimeCardController extends Controller
{
    public function index()
    {
        $year = now()->year;
        $month = now()->month;
        $day = now()->day;
        $attendance = Auth::user()->attendances()->byDay($year, $month, $day)->first();
        return view('user.timecard.index', compact('attendance'));
    }
    
    /* 出勤打刻 */
    public function clockIn(Request $request)
    {
        $user = Auth::user();
        $timestamp = Carbon::parse($request->input('timestamp'));
        $today = $timestamp->copy()->startOfDay();
        
        /* すでに出勤済みの場合は何もしない */
        $attendance = $user->attendances()->byDay($today->year, $today->month, $today->day)->first();
        if( $attendance ) {
            return redirect()->route('user.timecard');
        }
        
        /* 新規作成 */
        $attendance = $user->attendances()->create([
            'date' => $today,
            'clock_in_at' => $timestamp,
            'status' => Attendance::STATUS_WORKING,
        ]);
        
        return redirect()->route('user.timecard');
    }
    
    /* 退勤打刻 */
    public function clockOut(Request $request)
    {
        $user = Auth::user();
        $timestamp = Carbon::parse($request->input('timestamp'));
        $today = $timestamp->copy()->startOfDay();
        
        /* 出勤済みか確認 */
        $attendance = $user->attendances()->byDay($today->year, $today->month, $today->day)
            ->where('status', Attendance::STATUS_WORKING)
            ->first();
        if (!$attendance) {
            return redirect()->route('user.timecard');
        }
        
        /* 退勤打刻 */
        $attendance->update([
            'clock_out_at' => $timestamp,
            'status' => Attendance::STATUS_LEFT,
        ]);
        
        return redirect()->route('user.timecard');
    }
    
    public function startBreak(Request $request)
    {
        $user = Auth::user();
        $timestamp = Carbon::parse($request->input('timestamp'));
        $today = $timestamp->copy()->startOfDay();
        
        /* 出勤済みか確認 */
        $attendance = $user->attendances()->byDay($today->year, $today->month, $today->day)
            ->where('status', Attendance::STATUS_WORKING)
            ->first();
        if (!$attendance) {
            return redirect()->route('user.timecard');
        }
        
        /* 休憩開始を記録 */
        $breakTime = BreakTime::create([
            'attendance_id' => $attendance->id,
            'start_at' => $timestamp,
        ]);
        
        /* 勤怠状態を更新 */
        $attendance->update(['status' => Attendance::STATUS_BREAK]);
        
        return redirect()->route('user.timecard');
    }

    public function endBreak(Request $request)
    {
        $user = Auth::user();
        $timestamp = Carbon::parse($request->input('timestamp'));
        $today = $timestamp->copy()->startOfDay();
        
        /* 出勤済みか確認 */
        $attendance = $user->attendances()->byDay($today->year, $today->month, $today->day)
            ->where('status', Attendance::STATUS_BREAK)
            ->first();
        if (!$attendance) {
            return redirect()->route('user.timecard');
        }
        
        /* 休憩終了を記録 */
        $breakTime = $attendance->breakTimes()->whereNull('end_at')->latest()->first();
        if ($breakTime) {
            $breakTime->update(['end_at' => $timestamp]);
        }
        
        /* 勤怠状態を更新 */
        $attendance->update(['status' => Attendance::STATUS_WORKING]);
        
        return redirect()->route('user.timecard');
    }
}
