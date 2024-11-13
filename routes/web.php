<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => ['web']], function () {
    // your routes here
    Route::get('/login', function () {
        return view('login');
    });
});
