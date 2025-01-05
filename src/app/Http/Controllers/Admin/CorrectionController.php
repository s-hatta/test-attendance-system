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
    
    public function show()
    {
        return view('admin.correction.show');
    }
}
