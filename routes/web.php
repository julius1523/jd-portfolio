<?php

use Illuminate\Support\Facades\Route;

Route::get('/media/{path}', function (string $path) {
    $disk = Storage::disk('public');

    if (!$disk->exists($path)) {
        abort(404);
    }

    return response()->file($disk->path($path));
})->where('path', '.*');

Route::middleware('prevent_back')
    ->get('/{any}', fn() => response()->view('app'))
    ->where('any', '.*');