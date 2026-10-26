<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', fn() => response()->view('app'))
    ->where('any', '.*');

Route::permanentRedirect(
    '/',
    'https://juliusdolana-portfolio.vercel.app/'
);