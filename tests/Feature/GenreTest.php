<?php

namespace Tests\Feature;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function test_getAllGenres_success(): void
    {
        Genre::factory()->create();

        $response = $this->getJson('api/genres');
        $response->assertOk()
        ->assertJson(function(AssertableJson $json){
            $json->hasAll(['message', 'success', 'data'])
            ->has('data', 1, function (AssertableJson $json) {
                $json->whereAllType(['name' => 'string','id' => 'integer']);
            });
        });

    }
}
