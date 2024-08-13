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

    public function test_returnBook_failureWrongEmail(): void
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

    public function test_returnBook_failureBookIdNotFound(): void
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

    public function test_returnBook_failureNotAlreadyClaimed(): void
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
}
