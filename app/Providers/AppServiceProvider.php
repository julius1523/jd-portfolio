<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{

    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('contact', fn(Request $request) => Limit::perMinute(3)->by($request->ip()));

        RateLimiter::for('login', fn(Request $request) => Limit::perMinute(5)->by($request->ip() . '|' . $request->input('email')));
    }
}
