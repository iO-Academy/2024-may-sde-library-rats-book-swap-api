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
        Book::factory()->create(['claimed' => 0]);

        $response = $this->getJson('api/books');

        // assertOK() which auto does 200
        $response->assertStatus()
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success', 'data'])
                    ->has('data', 1, function (AssertableJson $json) {
                        // Look at whereAllType to assert json datatypes
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
        // Never a blank line at the start of a method
        Book::factory()->create();

        $response = $this->get('/api/books/1');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success', 'data'])
                    ->has('data', function (AssertableJson $json) {
                        $json->hasAll([
                            'id',
                            'title',
                            'author',
                            'image',
                            'year',
                            'blurb',
                            'page_count',
                            'claimed',
                            'claimed_by_name',
                            'genre_id',
                            'user_id',
                            'claimed_by_email',
                            'genre',
                            'reviews',
                            'created_at',
                            'updated_at'
                        ]);
                    });
            });
    }

    public function test_getBookByID_failure(): void
    {
        Book::factory()->create();

        $response = $this->get('/api/books/3');

        $response->assertStatus(404)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });
    }

    public function test_returnBook_success(): void
    {
        Book::factory()->create(['claimed_by_email' => 'test@test.com', 'claimed' => 1]);

        $testData = [
            'email' => 'test@test.com'
        ];

        $response = $this->putJson('/api/books/return/1', $testData);

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success');
            });

        $this->assertDatabaseHas('books', ['id' => 1, 'claimed' => 0, 'claimed_by_email' => null]);
    }

    public function test_returnBook_failure_WrongEmail(): void
    {
        Book::factory()->create(['claimed_by_email' => 'test@test.com', 'claimed' => 1]);

        $testData = [
            'email' => 'te@test.com'
        ];

        $response = $this->putJson('/api/books/return/1', $testData);

        $response->assertStatus(400)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success');
            });

        $this->assertDatabaseHas('books', ['id' => 1, 'claimed' => 1, 'claimed_by_email' => 'test@test.com']);
    }

    public function test_returnBook_failure_BookIdNotFound(): void
    {
        Book::factory()->create(['claimed_by_email' => 'test@test.com', 'claimed' => 1]);

        $testData = [
            'email' => 'test@test.com'
        ];

        $response = $this->putJson('/api/books/return/2', $testData);

        $response->assertStatus(404)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success');
            });

        $this->assertDatabaseMissing('books', ['id' => 2]);
    }

    public function test_returnBook_failure_NotAlreadyClaimed(): void
    {
        Book::factory()->create(['claimed_by_email' => 'test@test.com', 'claimed' => 0]);

        $testData = [
            'email' => 'test@test.com'
        ];

        $response = $this->putJson('/api/books/return/1', $testData);

        $response->assertStatus(400)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success');
            });

        $this->assertDatabaseHas('books', ['id' => 1, 'claimed' => 0, 'claimed_by_email' => 'test@test.com']);
    }

    public function test_claimBook_success(): void
    {
        Book::factory()->create(['claimed' => 0]);

        $testData = ([
            'name' => 'test',
            'email' => 'test@test.com'
        ]);

        $response = $this->putJson('/api/books/claim/1', $testData);

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success');
            });

        $this->assertDatabaseHas('books', ['id' => 1,
            'claimed' => 1,
            'claimed_by_email' => 'test@test.com',
            'claimed_by_name' => 'test'
        ]);

    }

    public function test_claimBook_failure_BookIdNotFound(): void
    {
        Book::factory()->create(['claimed' => 0]);

        $testData = [
            'name' => 'test',
            'email' => 'test@test.com'
        ];

        $response = $this->putJson('/api/books/claim/2', $testData);

        $response->assertStatus(404)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success');
            });

        $this->assertDatabaseMissing('books', ['id' => 2]);
    }

    public function test_claimBook_failure_BookAlreadyClaimed(): void
    {
        Book::factory()->create(['claimed_by_name' => 'test', 'claimed_by_email' => 'test@test.com', 'claimed' => 1]);

        $testData = [
            'name' => 'test',
            'email' => 'test@test.com'
        ];

        $response = $this->putJson('/api/books/claim/1', $testData);

        $response->assertStatus(400)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success');
            });

        $this->assertDatabaseHas('books', ['id' => 1,
            'claimed' => 1,
            'claimed_by_name' => 'test',
            'claimed_by_email' => 'test@test.com']);
    }

    public function test_getBookGenre_success(): void
    {
        Book::factory()->create(['claimed' => 0]);

        $response = $this->getJson('api/books?genre=1');

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
                        ])
                            ->where(
                                'genre_id', 1
                            );
                    });
            });
    }

    public function test_addBook_success(): void
    {
        Genre::factory()->create();

        $testData = [
            'title' => 'A boooooook',
            'author' => 'An author',
            'genre_id' => 1
        ];

        $response = $this->postJson('/api/books', $testData);

        $response->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });

        $this->assertDatabaseHas('books', $testData);
    }

    public function test_addBookAllData_success(): void
    {
        Genre::factory()->create();

        $testData = [
            'title' => 'A boooooook',
            'author' => 'An author',
            'genre_id' => 1,
            'blurb' => 'Blurb',
            'image' => 'image',
            'year' => 2024,
            'page_count' => 223,
            'claimed' => 0
        ];

        $response = $this->postJson('/api/books', $testData);

        $response->assertStatus(201)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success']);
            });

        $this->assertDatabaseHas('books', $testData);
    }

    public function test_addBook_failure(): void
    {
        $testData = [];

        $response = $this->postJson('/api/books', $testData);

        $response->assertStatus(422)
            ->assertInvalid([
                'title' => 'The title field is required',
                'author' => 'The author field is required',
                'genre_id' => 'The genre id field is required'
            ]);

        $this->assertDatabaseMissing('books', [
            'title' => 'Book',
            'author' => 'An author',
            'genre_id' => 1
        ]);
    }

    public function test_addBookAllData_failure(): void
    {
        $testData = [
            'title' => 3,
            'author' => 2456,
            'genre_id' => 'History',
            'blurb' => false,
            'image' => 2,
            'year' => 'Twenty Twenty Four',
            'page_count' => true,
            'claimed' => 'yes'
        ];

        $response = $this->postJson('/api/books', $testData);

        $response->assertStatus(422)
            ->assertInvalid([
                'title' => 'The title field must be a string',
                'author' => 'The author field must be a string',
                'genre_id' => 'The genre id field must be an integer',
                'blurb' => 'The blurb field must be a string',
                'image' => 'The image field must be a string',
                'year' => 'The year field must be an integer',
                'claimed' => 'The claimed field must be true or false'
            ]);

        $this->assertDatabaseMissing('books', [
            'title' => 3,
            'author' => 2456,
            'genre_id' => 'History',
            'blurb' => false,
            'image' => 2,
            'year' => 'Twenty Twenty Four',
            'page_count' => true,
            'claimed' => 'yes'
        ]);
    }

    public function test_getClaimedBooks_success(): void
    {
        Book::factory()->create(['claimed' => 1]);
        Book::factory()->create(['claimed' => 0]);

        $response = $this->getJson('api/books?claimed=1');

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

    public function test_getBookSearch_success(): void
    {
        Book::factory()->create(['title' => 'testybook title', 'author' => 'author', 'blurb' => 'blurb', 'claimed' => 0]);
        Book::factory()->create(['title' => 'title', 'author' => 'testerson', 'blurb' => 'blurb', 'claimed' => 0]);
        Book::factory()->create(['title' => 'title', 'author' => 'author', 'blurb' => 'testybook', 'claimed' => 0]);
        Book::factory()->create(['title' => 'title', 'author' => 'author', 'blurb' => 'blurb', 'claimed' => 0]);

        $response = $this->getJson('api/books?search=test');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success', 'data'])
                    ->has('data', 3, function (AssertableJson $json) {
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

    public function test_getBookSearchNoResult_success(): void
    {
        Book::factory()->create(['title' => 'title', 'author' => 'author', 'blurb' => 'blurb', 'claimed' => 0]);

        $response = $this->getJson('api/books?search=test');

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll(['message', 'success', 'data'])
                    ->has('data', 0);
            });
    }
}
