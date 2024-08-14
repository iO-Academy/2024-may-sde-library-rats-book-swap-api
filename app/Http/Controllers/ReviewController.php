<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\JsonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public Review $review;
    public JsonService $jsonService;

    public function __construct(Review $review, JsonService $jsonService)
    {
        $this->review = $review;
        $this->jsonService = $jsonService;
    }

    public function addReview(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'rating' => 'required|integer|min:0|max:5',
            'review' => 'required|string|max:255',
            'book_id' => 'required|exists:books,id',
        ]);

        $review = new Review;

        $review->name = $request->name;
        $review->rating = $request->rating;
        $review->review = $request->review;
        $review->book_id = $request->book_id;

        if ($review->save()) {
            return $this->jsonService->get('Review created', true, status:201);
        }

        return $this->jsonService->get('Unexpected error occurred', false, status:500);
    }
}
