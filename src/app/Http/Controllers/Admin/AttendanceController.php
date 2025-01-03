<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * 勤怠一覧画面表示
     */
    public function index(Request $request)
    {
        Carbon::setLocale('ja');
        
        /* 表示する年月日を決定 (パラメータがない場合は当日) */
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $day = $request->input('day', now()->day);
        $currentDate = Carbon::create($year, $month, $day);
        
        /* 前日と翌日 */
        $prevDate = $currentDate->copy()->subDay();
        $nextDate = $currentDate->copy()->addDay();
        
        /* 指定日の勤怠データを取得 */
        $attendances = Attendance::with('breakTimes','user')
            ->byDate($currentDate)
            ->get();
        
        $dates = collect();
        foreach( $attendances as $attendance ) {
            /* 休憩時間の合計を計算 */
            $totalBreakTime = 0;
            foreach( $attendance->breakTimes as $breakTime ) {
                if( $breakTime->start_at && $breakTime->end_at )
                $totalBreakTime += $breakTime->start_at->diffInMinutes( $breakTime->end_at );
            }
            $attendance->total_break_time = ( $totalBreakTime > 0 )? sprintf('%d:%02d', floor($totalBreakTime / 60), $totalBreakTime % 60) : null;
            
            /* 勤務時間を計算 (休憩時間を除く) */
            if ($attendance->clock_in_at && $attendance->clock_out_at) {
                $totalWorkTime = $attendance->clock_in_at->diffInMinutes($attendance->clock_out_at) - $totalBreakTime;
                $attendance->total_work_time = sprintf('%d:%02d', floor($totalWorkTime / 60), $totalWorkTime % 60);
            } else {
                $totalWorkTime = null;
            }
            
            /* 配列に追加 */
            $dates->push([
                'date' => $currentDate->copy(),
                'attendance' => $attendance,
            ]);
        }
        
        return view('admin.attendance.index', compact('dates', 'currentDate', 'prevDate', 'nextDate'));
    }
    
    public function show()
    {
        return view('admin.attendance.show');
    }
}
