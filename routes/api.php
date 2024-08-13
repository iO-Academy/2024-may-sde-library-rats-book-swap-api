<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/books', [\App\Http\Controllers\BookController::class, 'getAllBooks']);
Route::get('/books/{id}', [\App\Http\Controllers\BookController::class, 'getBookById']);
Route::put('/books/claim/{id}', [\App\Http\Controllers\BookController::class, 'claimBook']);
Route::put('/books/return/{id}', [\App\Http\Controllers\BookController::class, 'returnBook']);
Route::get('/genres', [\App\Http\Controllers\GenreController::class, 'getAllGenres']);
