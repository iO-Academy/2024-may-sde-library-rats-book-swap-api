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
        Book::factory()->create(['claimed_by_email' =>'test@test.com', 'claimed'=> 1]);

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
        Book::factory()->create(['claimed_by_email'=> 'test@test.com', 'claimed'=>1]);

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
        Book::factory()->create(['claimed_by_email'=> 'test@test.com', 'claimed'=>1]);

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
        Book::factory()->create(['claimed_by_email'=>'test@test.com','claimed' => 0]);

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
        Book::factory()->create(['claimed'=>0]);

        $testData = ([
           'name'=>'test',
           'email'=>'test@test.com'
        ]);

        $response = $this->putJson('/api/books/claim/1', $testData);

        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success');
            });

        $this->assertDatabaseHas('books', ['id' => 1,
            'claimed' => 1,
            'claimed_by_email' => 'test@test.com',
            'claimed_by_name'=> 'test'
        ]);

    }

    public function test_claimBook_failure_BookIdNotFound(): void
    {
        Book::factory()->create(['claimed'=>0]);

        $testData = [
            'name'=> 'test',
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
        Book::factory()->create(['claimed_by_name'=>'test','claimed_by_email'=>'test@test.com','claimed' => 1]);

        $testData = [
            'name'=> 'test',
            'email' => 'test@test.com'
        ];

        $response = $this->putJson('/api/books/claim/1', $testData);

        $response->assertStatus(400)
            ->assertJson(function (AssertableJson $json) {
                $json->hasAll('message', 'success');
            });

        $this->assertDatabaseHas('books', ['id' => 1,
            'claimed' => 1,
            'claimed_by_name'=> 'test',
            'claimed_by_email' => 'test@test.com']);
    }

    public function test_getBookGenre_success(): void
    {
        Book::factory()->create();

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
}
