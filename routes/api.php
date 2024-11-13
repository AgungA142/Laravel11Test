<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

//prefix for auth
Route::middleware('web')->prefix('auth')->group(function () {
    Route::get('/google/redirect', [AuthController::class, 'googleredirect']);
    Route::get('/google/callback', [AuthController::class, 'googleLogin']);

});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])-> middleware('auth:sanctum');
})




?>