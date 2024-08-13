<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Http\Request;

class BookController extends Controller
{

    public Book $book;

    public function __construct(Book $book)
    {
        $this->book = $book;
    }
    public function getAllBooks()
    {
        $books = $this->book->with('genre')->get()->makeHidden([
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

    public function claimBook(int $id, Request $request)
    {
        $request->validate([
            'name'=>'required | string',
            'email'=>'required | string'
        ]);

        $book = $this->book->find($id);

        if(!$book){
            return response()->json([
                'message'=> "Book {$id} not found",
                'success'=> false
            ],404);
        }
        if ($book->claimed == 1){
            return response()->json([
                'message'=> "Book {id} is already claimed",
                'success'=>false
            ], 400);
        }

        $book->name = $request->name;
        $book->email = $request->email;
        $book->claimed = 1;
        $book->save();
        return response()->json([
            'message'=> "Book {$id} was claimed",
            'success'=> true,
            'data'=> $book
        ]);


    }
}
