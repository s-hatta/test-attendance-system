<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Requests\CorrectionRequest;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('user.attendance.index');
    }
    
    /**
     * 勤怠詳細画面表示
     */
    public function show($id)
    {
        $user = Auth::user();
        $attendance = $user->attendances()->where('id',$id)->first();
        
        /* 勤怠記録がない場合はNotFound */
        if(!$attendance) {
            abort(404);
        }
        
        /**
         * 日付、時刻は4桁の数値で扱う
         */
        $time = strtotime($attendance->date);
        $year = date('Y', $time);
        $date = date('md', $time);
        $clockIn = date('Hi', strtotime( $attendance->clock_in_at ));
        $clockOut = date('Hi', strtotime( $attendance->clock_out_at ));
        $inBreakTimes = $attendance->breakTimes()->get();
        $outBreakTimes = [];
        foreach( $inBreakTimes as $breakTime ) {
            $outBreakTimes[] = [
                'start' => date('Hi', strtotime( $breakTime->start_at )),
                'end' => date('Hi', strtotime( $breakTime->end_at )),
            
            ];
        }
        $param = [
            'name' => $user->name,
            'year' => $year,
            'date' => $date,
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
            'break_times' => $outBreakTimes,
            'remark' => $attendance->remark,
        ];
        return view('user.attendance.show', compact('id','param'));
    }
}
