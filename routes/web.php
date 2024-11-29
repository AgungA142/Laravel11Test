<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::group(['middleware' => ['web']], function () {
//     // your routes here
//     Route::get('/login', function () {
//         return view('login');
//     });
// });

Route::prefix('admin')->group(function () {
    // Route::get('/dashboard', function () {
    //     return view('admin.dashboard');
    // });
    // Route::get('/users', function () {
    //     return view('admin.users');
    // });
    // Route::get('/books', function () {
    //     return view('admin.books');
    // });
    Route::get('/updateBook/{id}', function ($id) {
        return view('admin.updateBook', ['id' => $id]);
    });
});
