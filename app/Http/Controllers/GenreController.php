<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    public Genre $genre;

    public function __construct(Genre $genre)
    {
        $this->genre = $genre;
    }

    public function getAllGenres()
    {
        $genres = $this->genre->get();
        return response()->json([
            'message' => 'Genres retrieved',
            'success' => true,
            'data' => $genres
        ]);
    }
}
