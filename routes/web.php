<?php

use Illuminate\Support\Facades\Route;

Route::permanentRedirect(
    '/',
    'https://juliusdolana-portfolio.vercel.app/'
);

Route::get('/{any}', fn() => response()->view('app'))
    ->where('any', '.*');