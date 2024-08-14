<?php

namespace App\Http\Controllers;

use App\Models\Genre;

class GenreController extends Controller
{
    public Genre $genre;

    public function __construct(Genre $genre)
    {
        $this->genre = $genre;
    }

    // return type json
    public function getAllGenres()
    {
        $genres = $this->genre->get();

        return response()->json([
            'message' => 'Genres retrieved',
            'success' => true,
            'data' => $genres,
        ]);
    }
}
