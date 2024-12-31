<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class TimeCardController extends Controller
{
    public function index()
    {
        return view('user.timecard.index');
    }
}
