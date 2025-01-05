<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'email', 'name')->get();
        return view('admin.staff.index', compact('users'));
    }
    
    public function show()
    {
        return view('admin.staff.show');
    }
}
