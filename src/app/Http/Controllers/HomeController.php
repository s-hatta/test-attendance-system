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
        return redirect()->route('user.login');
    }
}
