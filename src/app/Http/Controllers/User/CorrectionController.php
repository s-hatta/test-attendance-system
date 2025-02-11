<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;
use Illuminate\Support\Facades\Auth;
use App\Enums\CorrectionStatus;

class CorrectionController extends Controller
{
    public function index()
    {
        $pendingCorrections = AttendanceCorrection::with('attendance','user')
            ->where('user_id', Auth::id())
            ->where('status', CorrectionStatus::PENDING->value)
            ->orderBy('date', 'asc')
            ->get();
        $approvedCorrections = AttendanceCorrection::with('attendance','user')
            ->where('user_id', Auth::id())
            ->where('status', CorrectionStatus::APPROVED->value)
            ->orderBy('date', 'asc')
            ->get();

        return view('user.correction.index', compact('pendingCorrections','approvedCorrections') );
    }
}
