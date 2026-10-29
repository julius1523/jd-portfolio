<?php

use Illuminate\Support\Facades\Route;

Route::permanentRedirect(
    '/',
    'https://youtube.com/'
);

Route::get('/{any}', fn() => response()->view('app'))
    ->where('any', '.*');