<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookController extends Controller
{
    // Get rid of this blank line
    public Book $book;
    public Genre $genre;

    public function __construct(Book $book, Genre $genre)
    {
        $this->book = $book;
        $this->genre = $genre;
    }

    // Add JsonResponse return type hints to all controller methods
    public function getAllBooks(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'string',
            'genre' => 'exists:genres,id'
        ]);

        $booksQuery = $this->book->query();

        // indentation?
        if ($request->search) {
                $booksQuery = $booksQuery->whereAny(['title', 'author', 'blurb'], 'LIKE', "%{$request->search}%");
            }

        if ($request->genre) {
            // i think you can skip passing in =, it's the default
            $booksQuery = $booksQuery->where('genre_id', '=', $request->genre);
        }

        if ($request->claimed){
            $booksQuery = $booksQuery->where('claimed', '=', $request->claimed);
        }
        else {
            $booksQuery = $booksQuery->where('claimed', '=', 0);
        }

        // Add the timestamps to the hidden property of your book model
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

        // Switch this to using with instead - it's more efficient
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
        // Be consistent with spaces around |
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
    public function addBook(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'year' => 'integer',
            'blurb' => 'string|max:255',
            'image' => 'string|max:255',
            'page_count' => 'integer',
            'claimed' => 'boolean',
            'genre_id' => 'required|integer|exists:genres,id',
        ]);

        $book = new book();
        $book->title = $request->title;
        $book->author = $request->author;
        $book->year = $request->year;
        $book->blurb = $request->blurb;
        $book->image = $request->image;
        $book->page_count = $request->page_count;
        $book->claimed = 0;
        $book->genre_id = $request->genre_id;

        if ($book->save()) {
            return response()->json([
                'message' => 'booked created',
                'success' => true
            ], 201);
        }

        return response()->json([
            'message' => 'book failured',
            'success' => false
        ], 500);
    }
}
