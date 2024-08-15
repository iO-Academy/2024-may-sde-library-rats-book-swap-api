<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use App\Services\JsonService;
use Illuminate\Http\JsonResponse;

class GenreController extends Controller
{
    public Genre $genre;

    public JsonService $jsonService;

    public function __construct(Genre $genre, JsonService $jsonService)
    {
        $this->genre = $genre;
        $this->jsonService = $jsonService;
    }

    public function getAllGenres(): JsonResponse
    {
        $genres = $this->genre->get();

        return $this->jsonService->get('Genres retrieved', true, $genres);
    }
}
