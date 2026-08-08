<?php

use Illuminate\Support\Facades\Route;

Route::middleware('prevent_back')
    ->get('/{any}', fn() => response()->view('app'))
    ->where('any', '.*');