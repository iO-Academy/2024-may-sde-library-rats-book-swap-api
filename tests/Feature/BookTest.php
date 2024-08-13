<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BookTest extends TestCase
{

    use DatabaseMigrations;
    /**
     * A basic feature test example.
     */
    public function test_returnBook_success(): void
    {
        Book::factory()->create();

//        $testData = [
//            'title' => 'the title',
//            'author' => 'author name',
//            'year' => 1992,
//            'blurb' => 'awesome blurb',
//            'image' => 'https://via.placeholder.com/640x480.png/0099dd?text=modi',
//            'claimed_by_name' => 'name',
//            'page_count' => '234',
//            'claimed' => 0,
//            'genre_id' => 1,
//            'user_id' => 1,
//            'claimed_by_email' => 'test@test.com'
//            ];

            $testData = [
                'email' => 'test@test.com'
            ];

        $response = $this->putJson('/api/books/return/1', $testData);

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success', 'data');
            });

        $this->assertDatabaseHas('books', ['id' => 1, 'claimed' => 0, 'claimed_by_email' => null]);
    }
}
