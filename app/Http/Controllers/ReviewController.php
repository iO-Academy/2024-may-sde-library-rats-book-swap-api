<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public Review $review;

    public function __construct(Review $review)
    {
        $this->review = $review;
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
            return response()->json([
                'message' => 'Review created',
                'success' => true,
            ], 201);
        }

        return response()->json([
            'message' => 'Unexpected error occurred',
            'success' => false,
        ], 500);
    }
}
