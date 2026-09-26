<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;

Route::get('/media/{path}', function (string $path) {
    if (!Storage::disk('public')->exists($path)) {
        abort(404);
    }

    $mimeType = match (strtolower(pathinfo($path, PATHINFO_EXTENSION))) {
        'jpg', 'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
        'pdf' => 'application/pdf',
        'gif' => 'image/gif',
        'svg' => 'image/svg+xml',
        default => 'application/octet-stream',
    };

    return response()->stream(function () use ($path) {
        $stream = Storage::disk('public')->readStream($path);
        fpassthru($stream);
        fclose($stream);
    }, 200, [
        'Content-Type' => $mimeType,
    ]);
})->where('path', '.*');


Route::middleware('prevent_back')
    ->get('/{any}', fn() => response()->view('app'))
    ->where('any', '.*');