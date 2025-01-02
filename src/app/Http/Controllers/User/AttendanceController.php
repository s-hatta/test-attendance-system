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
