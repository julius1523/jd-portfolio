<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', fn() => response()
    ->view('app')
    ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
    ->header('Pragma', 'no-cache')
    ->header('Expires', '0'))->where('any', '.*');