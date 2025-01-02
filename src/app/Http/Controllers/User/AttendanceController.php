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
}
