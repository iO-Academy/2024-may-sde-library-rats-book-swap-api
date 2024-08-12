<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function getAllBooks()
    {
    $books = Book::all()->makeHidden(['blurb', 'claimed_by_name', 'page_count', 'claimed', 'user_id', 'created_at', 'updated_at' ]);

    return response()->json([
        'message' => 'Books successfully retrieved',
        'success'=> true,
        'data' => $books
    ]);
    }
}
