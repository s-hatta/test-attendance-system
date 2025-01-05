<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StaffController extends Controller
{
    /**
     * スタッフ一覧画面表示
     */
    public function index()
    {
        $users = User::select('id', 'email', 'name')->get();
        return view('admin.staff.index', compact('users'));
    }
    
    /**
     * スタッフ別勤怠一覧画面表示
     */
    public function show($id, Request $request)
    {
        Carbon::setLocale('ja');
        
        $user = User::where('id',$id)->first();
        $userId = $user->id;
        $name = $user->name;
        
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
                $attendance->total_break_time = ( $totalBreakTime > 0 )? sprintf('%d:%02d', floor($totalBreakTime / 60), $totalBreakTime % 60) : null;
                
                /* 勤務時間を計算 (休憩時間を除く) */
                if ($attendance->clock_in_at && $attendance->clock_out_at) {
                    $totalWorkTime = $attendance->clock_in_at->diffInMinutes($attendance->clock_out_at) - $totalBreakTime;
                    $attendance->total_work_time = sprintf('%d:%02d', floor($totalWorkTime / 60), $totalWorkTime % 60);
                } else {
                    $totalWorkTime = null;
                }
            }
            
            /* 配列に追加 */
            $dates->push([
                'date' => $date->copy(),
                'attendance' => $attendance
            ]);
        }
        
        return view('admin.staff.show', compact('userId', 'name', 'dates', 'currentDate', 'prevMonth', 'nextMonth'));
    }
}
