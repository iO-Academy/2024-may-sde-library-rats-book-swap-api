<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/books', [\App\Http\Controllers\BookController::class, 'getAllBooks']);

Route::put('/api/books/claim/{id}', [\App\Http\Controllers\BookController::class, 'claimBook']);
