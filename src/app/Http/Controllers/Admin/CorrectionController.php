<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CorrectionController extends Controller
{
    public function index()
    {
        return view('admin.correction.index');
    }
    
    public function show()
    {
        return view('admin.correction.show');
    }
}
