<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\IconController;
use App\Http\Controllers\Admin\HomeContentController;
use App\Http\Controllers\Admin\AboutContentController;
use App\Http\Controllers\Admin\ProjectContentController;
use App\Http\Controllers\Admin\ContactContentController;
use App\Http\Controllers\Admin\SystemSettingsController;


Route::post('/contact', [ContactController::class, 'store']);

// Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user()->only(['name', 'email']));
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/icons/getIcons', [IconController::class, 'getIcons']);

    Route::get('/getHomeContent', [HomeContentController::class, 'getHomeContent']);
    Route::post('/updateHomeContent', [HomeContentController::class, 'updateHomeContent']);
    Route::get('/getAboutContent', [AboutContentController::class, 'getAboutContent']);
    Route::post('/updateAboutContent', [AboutContentController::class, 'updateAboutContent']);
    Route::get('/getProjectContent', [ProjectContentController::class, 'getProjectContent']);
    Route::post('/updateProjectContent', [ProjectContentController::class, 'updateProjectContent']);
    Route::get('/getContactContent', [ContactContentController::class, 'getContactContent']);
    Route::post('/updateContactContent', [ContactContentController::class, 'updateContactContent']);

    Route::get('/getSystemSettings', [SystemSettingsController::class, 'getSystemSettings']);
    Route::post('/updateSystemSettings', [SystemSettingsController::class, 'updateSystemSettings']);
});

Route::get('/public/home', [HomeContentController::class, 'getHomeContent']);
Route::get('/public/about', [AboutContentController::class, 'getAboutContent']);
Route::get('/public/projects', [ProjectContentController::class, 'getProjectContent']);
Route::get('/public/contact', [ContactContentController::class, 'getContactContent']);