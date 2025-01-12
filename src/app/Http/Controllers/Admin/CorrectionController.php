<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
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
                'start' => $breakTimeCorrection->start_at->format('H:i'),
                'end' => $breakTimeCorrection->end_at->format('H:i'),
            ];
        })->toArray();
        
        $param = [
            'name' => $attendanceCorrection->user->name,
            'year' => ($attendanceCorrection->date)? $attendanceCorrection->date->format('Y年'):null,
            'date' => ($attendanceCorrection->date)? $attendanceCorrection->date->format('n月j日'):null,
            'clock_in' => ($attendanceCorrection->clock_in_at)? $attendanceCorrection->clock_in_at->format('H:i'):null,
            'clock_out' => ($attendanceCorrection->clock_out_at)? $attendanceCorrection->clock_out_at->format('H:i'):null,
            'break_times' => $breakTimeCorrections,
            'remark' => $attendanceCorrection->remark,
            'status' => $attendanceCorrection->status,
        ];
        return view('admin.correction.show', compact('attendance_correct_request','param'));
    }
    
    /**
     * 申請承認処理
     */
    public function update($attendance_correct_request)
    {
        $attendanceCorrection = AttendanceCorrection::with('breakTimeCorrections')
            ->where('id', $attendance_correct_request)
            ->firstOrFail();
        $attendance = Attendance::with('breakTimes')
            ->where('id', $attendanceCorrection->attendance_id)
            ->firstOrFail();
        
        /* 勤怠情報更新 */
        $attendance->clock_in_at = $attendanceCorrection->clock_in_at;
        $attendance->clock_out_at = $attendanceCorrection->clock_out_at;
        $attendance->remark = $attendanceCorrection->remark;
        $attendance->save();
        
        /* 休憩時間更新 */
        $attendance->breakTimes()->delete();
        foreach ($attendanceCorrection->breakTimeCorrections as $breakTimeCorrection) {
            $attendance->breakTimes()->create([
                'start_at' => $breakTimeCorrection->start_at,
                'end_at' => $breakTimeCorrection->end_at
            ]);
        }
        
        /* 申請ステータスを承認済みに更新 */
        $attendanceCorrection->status = CorrectionStatus::APPROVED->value;
        $attendanceCorrection->save();
        
        return redirect()->route('admin.correction.show', ['attendance_correct_request'=>$attendance_correct_request]);
    }
}
