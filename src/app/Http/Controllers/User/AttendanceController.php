<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        return view('user.attendance.index');
    }
    
    public function show()
    {
        return view('user.attendance.show');
    }
}
