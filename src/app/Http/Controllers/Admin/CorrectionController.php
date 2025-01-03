<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AttendanceCorrection;

class CorrectionController extends Controller
{
    /**
     * 申請一覧画面表示
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 0);
        
        $corrections = AttendanceCorrection::with('attendance','user')
            ->where('status', $status)
            ->orderBy('date', 'asc')
            ->get();
            
        return view('admin.correction.index', compact('corrections','status') );
    }
    
    public function show()
    {
        return view('admin.correction.show');
    }
}
