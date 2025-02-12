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

        return view('user.attendance.index', compact('dates', 'currentDate', 'prevMonth', 'nextMonth'));
    }

    /**
     * 勤怠詳細画面表示
     */
    public function show($id, Request $request)
    {
        $user = Auth::user();
        if( $id > 0 ) {
            $attendance = $user->attendances()
                ->with('breakTimes')
                ->where('id', $id)
                ->firstOrFail();
        } else {
            $attendance = new Attendance();
        }

        $breakTimes = $attendance->breakTimes->map(function ($breakTime) {
            return [
                'start' => $breakTime->start_at->format('Hi'),
                'end' => $breakTime->end_at->format('Hi'),
            ];
        })->toArray();

        $param = [
            'name' => $user->name,
            'year' => ($attendance->date)? $attendance->date->format('Y'):null,
            'date' => ($attendance->date)? $attendance->date->format('md'):null,
            'clock_in' => ($attendance->clock_in_at)? $attendance->clock_in_at->format('Hi'):null,
            'clock_out' => ($attendance->clock_out_at)? $attendance->clock_out_at->format('Hi'):null,
            'break_times' => $breakTimes,
            'remark' => $attendance->remark,
        ];

        /* 承認待ちの申請がある場合はフラグを追加 */
        $attendanceCorrection = $attendance->attendanceCorrections->where('stauts',CorrectionStatus::PENDING->value)->first();
        if( isset($attendanceCorrection ) ) {
            $param['is_pending'] = true;
        }

        return view('user.attendance.show', compact('id','param'));
    }

    /**
     * 勤怠修正申請登録
     */
    public function store($id, CorrectionRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        $attendance = $user->attendances()->where('id',$id)->first();
        if( !$attendance ) {
            $attendance = $user->attendances()->create([
                'date' => $validated['date'],
            ]);
        }
        $attendanceCorrection = $attendance->attendanceCorrections()->create([
            'user_id' => $user->id,
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
