<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;
use App\Enums\CorrectionStatus;

class CorrectionController extends Controller
{
    /**
     * 申請一覧画面表示
     */
    public function index()
    {
        $pendingCorrections = AttendanceCorrection::with('attendance','user')
            ->where('status', CorrectionStatus::PENDING->value)
            ->orderBy('date', 'asc')
            ->get();
        $approvedCorrections = AttendanceCorrection::with('attendance','user')
            ->where('status', CorrectionStatus::APPROVED->value)
            ->orderBy('date', 'asc')
            ->get();
        
        return view('admin.correction.index', compact('pendingCorrections','approvedCorrections') );
    }
    
    /**
     * 申請承認画面表示
     */
	public function show($attendance_correct_request)
    {
        $attendanceCorrection = AttendanceCorrection::with('breakTimeCorrections','user')
            ->where('id', $attendance_correct_request)
            ->firstOrFail();
        $breakTimeCorrections = $attendanceCorrection->breakTimeCorrections->map(function ($breakTimeCorrection) {
            return [
                'start' => $breakTimeCorrection->start_at->format('Hi'),
                'end' => $breakTimeCorrection->end_at->format('Hi'),
            ];
        })->toArray();
        
        $param = [
            'name' => $attendanceCorrection->user->name,
            'year' => ($attendanceCorrection->date)? $attendanceCorrection->date->format('Y'):null,
            'date' => ($attendanceCorrection->date)? $attendanceCorrection->date->format('md'):null,
            'clock_in' => ($attendanceCorrection->clock_in_at)? $attendanceCorrection->clock_in_at->format('Hi'):null,
            'clock_out' => ($attendanceCorrection->clock_out_at)? $attendanceCorrection->clock_out_at->format('Hi'):null,
            'break_times' => $breakTimeCorrections,
            'remark' => $attendanceCorrection->remark,
        ];
        return view('admin.correction.show', compact('attendance_correct_request','param'));
    }
}
