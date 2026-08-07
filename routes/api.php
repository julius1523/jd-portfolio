<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeContentController;

Route::post('/contact', [ContactController::class, 'store']);

// Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user()->only(['name', 'email']));
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/getHomeContent', [HomeContentController::class, 'getHomeContent']);
    Route::post('/updateHomeContent', [HomeContentController::class, 'updateHomeContent']);
});