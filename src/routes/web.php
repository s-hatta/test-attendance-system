<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User;

/* ホームページ */
Route::get('/', [HomeController::class, 'index']);

/* 一般ユーザー用ルート */
Route::name('user.')->group(function () {
    Route::get('/login', [User\LoginController::class, 'create'])->name('login');
});
