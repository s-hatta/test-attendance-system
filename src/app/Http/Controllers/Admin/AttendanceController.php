<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\BreakTime;
use Illuminate\Http\Request;
use App\Http\Requests\CorrectionRequest;
use App\Enums\AttendanceStatus;
use App\Enums\CorrectionStatus;
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
    
    /**
     * 勤怠詳細画面表示
     */
    public function show($id, Request $request)
    {
        /* 勤怠詳細がない場合は仮パラメータを作成 */
        if( $id == 0 ) {
            $user = User::where('id',$request->userId)->first();
            $breakTime = New BreakTime([
                    'start' => null,
                    'end' => null,
            ]);
            $param = [
                'name' => $user->name,
                'year' => date('Y',strtotime($request['date'])),
                'date' => date('md',strtotime($request['date'])),
                'clock_in' => null,
                'clock_out' => null,
                'break_times' => $breakTime->toArray(),
                'remark' => null,
            ];
            return view('admin.attendance.show', compact('id','param'));
        }
        
        $attendance = Attendance::with('breakTimes')
            ->where('id', $id)
            ->firstOrFail();
        $breakTimes = $attendance->breakTimes->map(function ($breakTime) {
            return [
                'start' => $breakTime->start_at->format('Hi'),
                'end' => $breakTime->end_at->format('Hi'),
            ];
        })->toArray();
        
        $param = [
            'name' => $attendance->user->name,
            'year' => ($attendance->date)? $attendance->date->format('Y'):null,
            'date' => ($attendance->date)? $attendance->date->format('md'):null,
            'clock_in' => ($attendance->clock_in_at)? $attendance->clock_in_at->format('Hi'):null,
            'clock_out' => ($attendance->clock_out_at)? $attendance->clock_out_at->format('Hi'):null,
            'break_times' => $breakTimes,
            'remark' => $attendance->remark,
        ];
        return view('admin.attendance.show', compact('id','param'));
    }
}
