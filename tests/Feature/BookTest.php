<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class BookTest extends TestCase
{
    use DatabaseMigrations;

    public function test_getAllBooks_success(): void
    {
        Book::factory()->create();

        $response = $this->getJson('api/books');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success', 'data'])
                    ->has('data', 1, function (AssertableJson $json) {
                        $json->hasAll([
                            'id',
                            'title',
                            'author',
                            'image',
                            'year',
                            'claimed_by_name',
                            'genre_id',
                            'claimed_by_email',
                            'genre'
                        ]);
                    });
            });
    }

    public function test_getBookById_success(): void
    {

        Book::factory()->create();

        $response = $this->get('/api/books/1');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success', 'data']);
            });

    }
}
