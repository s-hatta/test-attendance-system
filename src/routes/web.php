<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\User;
use App\Http\Controllers\Admin;

/* ホームページ */
Route::get('/', [HomeController::class, 'index']);

/* 一般ユーザー・管理者共通ルート */
Route::middleware('auth:user,admin')->group(function () {
    Route::get('/stamp_correction_request/list', function () {
        if (Auth::guard('user')->check()) {
            return app()->make(User\CorrectionController::class)->index();
        }
        if (Auth::guard('admin')->check()) {
            return app()->make(Admin\CorrectionController::class)->index();
        }
        return redirect()->route('user.login');
    })->name('correction.index');
});
Route::get('/email/verify/{id}/{hash}', [User\LoginController::class, 'verify'])->name('verification.verify');
    
/* 一般ユーザー用ルート */
Route::name('user.')->group(function () {
    Route::middleware('guest:user')->group(function () {
        Route::get('/register', [User\RegisterController::class, 'create'])->name('register');
        Route::post('/register', [User\RegisterController::class, 'store']);
        Route::get('/login', [User\LoginController::class, 'create'])->name('login');
        Route::post('/login', [User\LoginController::class, 'store']);
    });
    
    Route::middleware('auth:user', 'verified')->group(function () {
        Route::post('/logout', [User\LoginController::class, 'destroy'])->name('logout');
        Route::get('/attendance', [User\TimeCardController::class, 'index'])->name('timecard');
        Route::post('/attendance/clock-in', [User\TimeCardController::class, 'clockIn'])->name('timecard.clockIn');
        Route::post('/attendance/clock-end', [User\TimeCardController::class, 'clockOut'])->name('timecard.clockOut');
        Route::post('/attendance/break-start', [User\TimeCardController::class, 'startBreak'])->name('timecard.startBreak');
        Route::post('/attendance/break-end', [User\TimeCardController::class, 'endBreak'])->name('timecard.endBreak');
        
        Route::get('/attendance/list', [User\AttendanceController::class, 'index'])->name('attendance.index');
        Route::get('/attendance/{id}', [User\AttendanceController::class, 'show'])->name('attendance.show');
        Route::post('/attendance/{id}', [User\AttendanceController::class, 'store'])->name('attendance.store');
    });
});

/* 管理者用ルート */
Route::name('admin.')->group(function () {
    Route::prefix('/admin')->group(function () {
        Route::middleware('guest:admin')->group(function () {
            Route::get('/login', [Admin\LoginController::class, 'create'])->name('login');
            Route::post('/login', [Admin\LoginController::class, 'store']);
        });
        
        Route::middleware('auth:admin')->group(function () {
            Route::post('/logout', [Admin\LoginController::class, 'destroy'])->name('logout');
            
            Route::get('/attendance/list', [Admin\AttendanceController::class, 'index'])->name('attendance.index');
            Route::get('/attendance/{id}', [Admin\AttendanceController::class, 'show'])->name('attendance.show');
            Route::post('/attendance/{id}', [Admin\AttendanceController::class, 'store'])->name('attendance.store');
            
            Route::get('/staff/list', [Admin\StaffController::class, 'index'])->name('staff.index');
            Route::get('/attendance/staff/{id}', [Admin\StaffController::class, 'show'])->name('staff.show');
            Route::post('/attendance/staff/{id}', [Admin\StaffController::class, 'export'])->name('staff.export');
        });
    });
    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [Admin\CorrectionController::class, 'show'])->name('correction.show');
    Route::post('/stamp_correction_request/approve/{attendance_correct_request}', [Admin\CorrectionController::class, 'update'])->name('correction.update');
});