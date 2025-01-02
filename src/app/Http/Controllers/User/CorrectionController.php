<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;
use Illuminate\Support\Facades\Auth;

class CorrectionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 0);
        
        $corrections = AttendanceCorrection::with('attendance')
            ->where('user_id', Auth::id())
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('user.correction.index', compact('corrections','status') );
    }
}