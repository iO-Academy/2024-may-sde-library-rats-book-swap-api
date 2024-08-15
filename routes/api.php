<?php

use App\Http\Controllers\BookController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(BookController::class)->group(function () {
    Route::get('/books', 'getAllBooks');
    Route::get('/books/{id}', 'getBookById');
    Route::put('books/claim/{id}', 'claimBook');
    Route::put('/books/return/{id}', 'returnBook');
    Route::post('/books', 'addBook');
});
Route::get('/genres', [\App\Http\Controllers\GenreController::class, 'getAllGenres']);
Route::post('/reviews', [\App\Http\Controllers\ReviewController::class, 'addReview']);
