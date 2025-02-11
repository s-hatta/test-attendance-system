<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if( request()->is('admin/*') ) {
            /* DoNothing */
        } else {
            $this->app->instance(RegisterResponse::class, new class implements RegisterResponse {
                public function toResponse($request)
                {
                    return redirect()->route('user.login')->with(['message'=>'送られたメール本文内のURLをクリックして登録を完了してください']);
                }
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        
        Fortify::registerView(function () {
            return view('user.auth.register');
        });
        
        Fortify::loginView(function () {
            return request()->is('admin/*') 
                ? view('admin.auth.login')
                : view('user.auth.login');
        });
        
        Fortify::verifyEmailView(function () {
            if( request()->is('admin/*') ) {
                auth()->logout();
                return view('admin.auth.login')->with(['message'=>'送られたメール本文内のURLをクリックして登録を完了してください']);
            } else {
                auth()->logout();
                return redirect()->route('user.login')->with(['message'=>'送られたメール本文内のURLをクリックして登録を完了してください']);
            }
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });
    }
}
