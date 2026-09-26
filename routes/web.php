<?php

use Illuminate\Support\Facades\Route;

Route::get('/debug-env', fn() => response()->json([
    'env_aws_url' => env('AWS_URL'),
    'config_public_url' => config('filesystems.disks.public.url'),
]));

Route::middleware('prevent_back')
    ->get('/{any}', fn() => response()->view('app'))
    ->where('any', '.*');