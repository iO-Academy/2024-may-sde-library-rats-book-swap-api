<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;

class BookController extends Controller
{

    public Book $book;
    public Genre $genre;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }
    public function getAllBooks(Request $request)
    {
        $booksQuery = $this->book->query();

        if (isset($request->genre)) {
            $booksQuery = $booksQuery->where('genre_id', '=', $request->genre);
        }

        $books = $booksQuery->with('genre')->get()->makeHidden([
            'blurb',
            'claimed_by_name',
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
}
