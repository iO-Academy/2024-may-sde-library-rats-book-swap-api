<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use DatabaseMigrations;

    public function test_addReview_success(): void
    {
        Book::factory()->create();

        $testData = [
           'name' => 'Test name',
           'rating' => 3,
           'review' => 'all good',
           'book_id' => 1
       ];

       $response = $this->postJson('api/reviews', $testData);

       $response->assertStatus(201)->assertJson(function (AssertableJson $json) {
           $json->hasAll(["message", "success"]);
       });

       $this->assertDatabaseHas('reviews', $testData);
    }

    public function test_addReview_failure(): void
    {
        $testData = [];

        $response = $this->postJson('api/reviews', $testData);

        $response->assertStatus(422)->assertInvalid([
            'name' => 'The name field is required',
            'rating' => 'The rating field is required',
            'review' => 'The review field is required',
            'book_id' => 'The book id field is required'
        ]);

        $this->assertDatabaseMissing('reviews', [
            'name' => 'name',
            'rating' => 2,
            'review' => 'a stunning review',
            'book_id' => 1
        ]);
    }

    public function test_addReview_dataType_failure(): void
    {
        $testData = [
            'name' => 3,
            'rating' => 6,
            'review' => 5/10,
            'book_id' => true
        ];

        $response = $this->postJson('api/reviews', $testData);

        $response->assertStatus(422)->assertInvalid([
            'name' => 'The name field must be a string',
            'rating' => 'The rating field must not be greater than 5',
            'review' => 'The review field must be a string',
            'book_id' => 'The selected book id is invalid'
        ]);

        $this->assertDatabaseMissing('reviews', [
            'name' => 3,
            'rating' => '4',
            'review' => 5/10,
            'book_id' => true
        ]);
    }
}
