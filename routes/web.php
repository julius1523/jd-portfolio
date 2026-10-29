<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', 'https://juliusdolana.vercel.app/');

Route::get('/{any}', fn() => response()->view('app'))
    ->where('any', '.*');