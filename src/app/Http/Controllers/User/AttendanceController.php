<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceCorrection;
use App\Models\BreakTimeCorrection;
use Illuminate\Http\Request;
use App\Http\Requests\CorrectionRequest;
use App\Enums\CorrectionStatus;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * 勤怠一覧画面表示
     */
    public function index(Request $request)
    {
        Carbon::setLocale('ja');
        
        $user = Auth::user();
        
        /* 表示する年月を決定 (パラメータがない場合は当月) */
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $currentDate = Carbon::create($year, $month, 1);
        
        /* 前月と翌月 */
        $prevMonth = $currentDate->copy()->subMonth();
        $nextMonth = $currentDate->copy()->addMonth();
        
        /* 指定月の勤怠データを取得 */
        $attendances = $user->attendances()
            ->with('breakTimes')
            ->byMonth($year, $month)
            ->get()
            ->keyBy( function( $attendance ) {
                return $attendance->date->format('Y-m-d');
            });
        
        /* 月の始まりから終わりまでの配列を作成 */
        $dates = collect();
        $startDate = $currentDate->copy()->startOfMonth();
        $endDate = $currentDate->copy()->endOfMonth();
        for( $date = $startDate; $date <= $endDate; $date->addDay( )) {
            $dateKey = $date->format('Y-m-d');
            $attendance = $attendances->get($dateKey);
            
            /* 勤怠データがあれば格納 */
            if( $attendance ) {
                
                /* 休憩時間の合計を計算 */
                $totalBreakTime = 0;
                foreach( $attendance->breakTimes as $breakTime ) {
                    if( $breakTime->start_at && $breakTime->end_at )
                    $totalBreakTime += $breakTime->start_at->diffInMinutes( $breakTime->end_at );
                }
                
                /* 勤務時間を計算 (休憩時間を除く) */
                $totalWorkTime = 0;
                if ($attendance->clock_in_at && $attendance->clock_out_at) {
                    $totalWorkTime = $attendance->clock_in_at->diffInMinutes($attendance->clock_out_at) - $totalBreakTime;
                }
                
                /* viewの表示用にフォーマット変換 */
                $attendance->total_break_time = sprintf('%d:%02d', floor($totalBreakTime / 60), $totalBreakTime % 60);
                $attendance->total_work_time = sprintf('%d:%02d', floor($totalWorkTime / 60), $totalWorkTime % 60);
            }
            
            /* 配列に追加 */
            $dates->push([
                'date' => $date->copy(),
                'attendance' => $attendance
            ]);
        }
        
        return view('user.attendance.index', compact('dates', 'currentDate', 'prevMonth', 'nextMonth'));
    }
    
    /**
     * 勤怠詳細画面表示
     */
    public function show($id)
    {
        $user = Auth::user();
        $attendance = $user->attendances()
            ->with('breakTimes')
            ->where('id', $id)
            ->firstOrFail();
        $breakTimes = $attendance->breakTimes->map(function ($breakTime) {
            return [
                'start' => $breakTime->start_at->format('Hi'),
                'end' => $breakTime->end_at->format('Hi'),
            ];
        })->toArray();
        
        $param = [
            'name' => $user->name,
            'year' => $attendance->date->format('Y'),
            'date' => $attendance->date->format('md'),
            'clock_in' => $attendance->clock_in_at->format('Hi'),
            'clock_out' => $attendance->clock_out_at->format('Hi'),
            'break_times' => $breakTimes,
            'remark' => $attendance->remark,
        ];
        return view('user.attendance.show', compact('id','param'));
    }
    
    /**
     * 勤怠修正申請登録
     */
    public function store(CorrectionRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();
        $attendanceCorrection = $user->attendanceCorrections()->create([
            'date' => $validated['date'],
            'clock_in_at' => $validated['clock_in_at'],
            'clock_out_at' => $validated['clock_out_at'],
            'status' => CorrectionStatus::PENDING->value,
            'remark' => $validated['remark'],
        ]);
        
        foreach( $validated['break_times'] as $breakTime ) {
            $attendanceCorrection->breakTimeCorrections()->create([
                'start_at' => $breakTime['start'],
                'end_at' => $breakTime['end'],
            ]);
        }
        return redirect()->route('user.attendance.index');
    }
}
