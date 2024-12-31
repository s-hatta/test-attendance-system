<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User;
use App\Http\Controllers\Admin;

/* ホームページ */
Route::get('/', [HomeController::class, 'index']);

/* 一般ユーザー用ルート */
Route::name('user.')->group(function () {
    Route::get('/register', [User\RegisterController::class, 'create'])->name('register');
    Route::post('/register', [User\RegisterController::class, 'store']);
    Route::get('/login', [User\LoginController::class, 'create'])->name('login');
    Route::post('/login', [User\LoginController::class, 'store']);
    
    Route::middleware('auth:user')->group(function () {
        Route::post('/logout', [User\LoginController::class, 'destroy'])->name('logout');
        Route::get('/attendance', [User\TimeCardController::class, 'index'])->name('timecard');
    });
});

/* 管理者用ルート */
Route::name('admin.')->prefix('/admin')->group(function () {
    Route::get('/login', [Admin\LoginController::class, 'create'])->name('login');
    Route::post('/login', [Admin\LoginController::class, 'store']);
    
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [Admin\LoginController::class, 'destroy'])->name('logout');
    });
});