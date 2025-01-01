<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::guard('user')->check()) {
            return redirect()->route('user.timecard');
        }
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.attendance.index');
        }
        return redirect()->route('user.login');
    }
}
