<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;

// prefix for auth
Route::middleware('web')->prefix('auth')->group(function () {
    Route::get('/google/redirect', [AuthController::class, 'googleredirect']);
    Route::get('/google/callback', [AuthController::class, 'googleLogin']);

});

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])-> middleware('auth:sanctum');
});

Route::prefix('user')->group(function () {
    Route::get('/getUser', [UserController::class, 'getAllUsers']);
    Route::post('/updatePassword', [UserController::class, 'updatePassword'])-> middleware('auth:sanctum');
    Route::post('/updateProfile', [UserController::class, 'updateProfile'])->middleware('auth:sanctum');
});

Route::prefix('admin/books')->group(function () {
    Route::get('/getBook', [BookController::class, 'getAllBooks']);
    Route::post('/createBook', [BookController::class, 'addBook'])-> middleware('auth:sanctum');
    Route::put('/updateBook/{id}', [BookController::class, 'updateBook'])->middleware('auth:sanctum');
    Route::delete('/deleteBook/{id}', [BookController::class, 'deleteBook'])->middleware('auth:sanctum');
    
})


?>