<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Genre;
use App\Services\JsonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class BookController extends Controller
{
    public Book $book;

    public Genre $genre;

    public JsonService $jsonService;

    public function __construct(Book $book, Genre $genre, JsonService $jsonService)
    {
        $this->book = $book;
        $this->genre = $genre;
        $this->jsonService = $jsonService;
    }

    public function getAllBooks(Request $request): JsonResponse
    {
        $request->validate([
            'search' => 'string',
            'genre' => 'exists:genres,id',
        ]);

        $booksQuery = $this->book->query();

        if ($request->search) {
            $booksQuery = $booksQuery->whereAny(['title', 'author', 'blurb'], 'LIKE', "%{$request->search}%");
        }

        if ($request->genre) {
            $booksQuery = $booksQuery->where('genre_id', $request->genre);
        }

        if ($request->claimed) {
            $booksQuery = $booksQuery->where('claimed', $request->claimed);
        } else {
            $booksQuery = $booksQuery->where('claimed', 0);
        }

        $books = $booksQuery->with('genre')->get()->makeHidden([
            'blurb',
            'name',
            'page_count',
            'claimed',
            'user_id',
        ]);

        return $this->jsonService->get('Books successfully retrieved', true, $books);
    }

    public function getBookById(int $id): JsonResponse
    {
        $book = $this->book->with('genre', 'reviews')->find($id);

        if (! $book) {
            return $this->jsonService->get("Book with ID {$id} not found", false, status: 404);
        }

        return $this->jsonService->get('book retrieved', true, $book);
    }

    public function claimBook(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string',
        ]);

        $book = $this->book->find($id);

        if (! $book) {
            return $this->jsonService->get("Book {$id} not found", false, status: 404);
        }
        if ($book->claimed == 1) {
            return $this->jsonService->get("Book {$id} is already claimed", false, status: 400);
        }

        $book->claimed_by_name = $request->name;
        $book->claimed_by_email = $request->email;
        $book->claimed = 1;
        $book->claimed_count = $book->claimed_count + 1;
        $book->save();

        return $this->jsonService->get("Book {$id} was claimed", true);
    }

    public function returnBook(int $id, Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|string',
        ]);

        $book = $this->book->find($id);

        if (! $book) {
            return $this->jsonService->get("Book {$id} not found", false, status: 404);
        }

        if ($book->claimed == 0) {
            return $this->jsonService->get("Book {$id} is not claimed", false, status: 400);
        }

        if ($book->claimed_by_email !== $request->email) {
            return $this->jsonService->get("Book {$id} was not returned. {$request->email} did not claim this book.", false, status: 400);
        }

        $book->claimed_by_name = null;
        $book->claimed_by_email = null;
        $book->claimed = 0;
        $book->save();

        return $this->jsonService->get("Book {$id} was returned", true);
    }

    public function addBook(Request $request): JsonResponse
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

        $book = new book;
        $book->title = $request->title;
        $book->author = $request->author;
        $book->year = $request->year;
        $book->blurb = $request->blurb;
        $book->image = $request->image;
        $book->page_count = $request->page_count;
        $book->claimed = 0;
        $book->genre_id = $request->genre_id;

        if ($book->save()) {
            return $this->jsonService->get('booked created', true, status: 201);
        }

        return $this->jsonService->get('book failured', false, status: 500);
    }

    public function getBookReport()
    {
        $popularBooks = $this->book->orderBy('claimed_count', 'DESC')->limit(3)->get();
        $leastPopular = $this->book->orderBy('claimed_count')->limit(3)->get();

        $genres = $this->genre->all();
        $genreClaimedCountMax = 0;
        $genreIdMax = 0;
        $genreClaimedCountMin = $this->book->where('genre_id', 1)->sum('claimed_count');
        $genreIdMin = 0;

        foreach ($genres as $genre) {
            if ($this->book->where('genre_id', $genre->id)->sum('claimed_count') > $genreClaimedCountMax) {
                $genreClaimedCountMax = $this->book->where('genre_id', $genre->id)->sum('claimed_count');
                $genreIdMax = $genre->id;
            }
            if ($this->book->where('genre_id', $genre->id)->sum('claimed_count') <= $genreClaimedCountMin) {
                $genreClaimedCountMin = $this->book->where('genre_id', $genre->id)->sum('claimed_count');
                $genreIdMin = $genre->id;
            }
        }

        $bestGenre = $this->genre->where('id', $genreIdMax)->get();
        $worstGenre = $this->genre->where('id', $genreIdMin)->get();

        return view('popularbooks', ['popularBooks' => $popularBooks, 'leastPopular' => $leastPopular, 'bestGenre' => $bestGenre, 'worstGenre' => $worstGenre]);
    }
}
