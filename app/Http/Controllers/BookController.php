<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;

class BookController extends Controller
{

    public Book $book;
    public Genre $genre;

    public function __construct(Book $book, Genre $genre)
    {
        $this->book = $book;
        $this->genre = $genre;
    }
    public function getAllBooks(Request $request)
    {
        $request->validate([
           'genre_id' => 'exists:genres,id'
        ]);

        $genreId = $request->genre;
        $genre = $this->genre->find($genreId);
        $booksQuery = $this->book->query();

        if (isset($request->genre)) {
            if (!$genre) {
                return response()->json([
                    'message' => 'Genre failed successfully',
                    'success'=> false
                ], 404);
            }
            $booksQuery = $booksQuery->where('genre_id', '=', $request->genre);
        }

        if (isset($request->claimed)){
            $booksQuery = $booksQuery->where('claimed', '=', $request->claimed);
        }
        else {
            $booksQuery = $booksQuery->where('claimed', '=', 0);
        }

        $books = $booksQuery->with('genre')->get()->makeHidden([
            'blurb',
            'name',
            'page_count',
            'claimed',
            'user_id',
            'created_at',
            'updated_at'
        ]);

        return response()->json([
            'message' => 'Books successfully retrieved',
            'success'=> true,
            'data' => $books
        ]);

    }
    public function getBookById(int $id) {

        $book = $this->book->find($id);

        if (!$book){
            return response()->json([
                'message' => "Book with ID {$id} not found",
                'success' => false
            ], 404);
        }

        $book->genre;
        $book->reviews;

        return response()->json([
            'message' => 'book retrieved',
            'success' => true,
            'data' => $book
        ]);
    }

    public function claimBook(int $id, Request $request)
    {
        $request->validate([
            'name' => 'required | string',
            'email' => 'required | string'
        ]);

        $book = $this->book->find($id);

        if(!$book){
            return response()->json([
                'message' => "Book {$id} not found",
                'success' => false
            ],404);
        }
        if ($book->claimed == 1){
            return response()->json([
                'message' => "Book {$id} is already claimed",
                'success' => false
            ], 400);
        }

        $book->claimed_by_name = $request->name;
        $book->claimed_by_email = $request->email;
        $book->claimed = 1;
        $book->save();
        return response()->json([
            'message' => "Book {$id} was claimed",
            'success' => true
        ]);
    }

    public function returnBook(int $id, Request $request)
    {
        $request->validate([
            'email' => 'required | string'
        ]);

        $book = $this->book->find($id);

        if(!$book){
            return response()->json([
                'message' => "Book {$id} not found",
                'success' => false
            ],404);
        }

        if ($book->claimed == 0){
            return response()->json([
                'message' => "Book {$id} is not claimed",
                'success' => false
            ], 400);
        }

        if ($book->claimed_by_email !== $request->email){
            return response()->json([
                'message' => "Book {$id} was not returned. {$request->email} did not claim this book.",
                'success' => false
            ], 400);
        }

        $book->claimed_by_name = null;
        $book->claimed_by_email = null;
        $book->claimed = 0;
        $book->save();
        return response()->json([
            'message' => "Book {$id} was returned",
            'success' => true
        ]);
    }
}
