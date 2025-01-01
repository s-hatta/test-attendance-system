<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance;

class TimeCardController extends Controller
{
    public function index()
    {
        $year = now()->year;
        $month = now()->month;
        $day = now()->day;
        $attendance = Auth::user()->attendances()->byDay($year, $month, $day)->first();
        return view('user.timecard.index', compact('attendance'));
    }
}
