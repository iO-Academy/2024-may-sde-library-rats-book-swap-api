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

    public function getBookById(int $id) {

        $book = $this->book->find($id);

        if (!$book){
            return response()->json([
                'message' => 'Book not found',
                'success' => false
            ], 404);
        }
        return response()->json([
            'message' => 'book retrived',
            'success' => true,
            'data' => $book
        ]);

    }
}
