<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/popularbooks', [\App\Http\Controllers\BookController::class, 'getBookReport']);
