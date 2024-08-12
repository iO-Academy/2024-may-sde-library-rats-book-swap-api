<?php

namespace App\Http\Controllers;

use App\Models\Book;
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
    $books = $this->book->with('genre')->get()->makeHidden(['blurb', 'claimed_by_name', 'page_count', 'claimed', 'user_id', 'created_at', 'updated_at']);


    return response()->json([
        'message' => 'Books successfully retrieved',
        'success'=> true,
        'data' => $books
    ]);
    }

//    public function claimBook(int $id, Request $request)
//    {
//        $book = Book::find($id);
//        $request->validate([
//            'name'=>'required | string',
//            'email'=>'required | email'
//        ]);
//
//        if(!$id){
//            return response()->json([
//                'message'=> 'Book {id} not found',
//                'success'=> false,
//            ]);
//        }
//    }
}
