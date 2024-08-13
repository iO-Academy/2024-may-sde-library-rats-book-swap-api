<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'author' => $this->faker->name(),
            'year' => $this->faker->year(),
            'blurb' => $this->faker->sentence(),
            'image' => $this->faker->imageUrl(),
            'claimed_by_name' => $this->faker->firstName(),
            'page_count' => rand(100,1000),
            'claimed' => rand(0,1),
            'genre_id' => Genre::factory(),
            'user_id' => null,
            'claimed_by_email' => $this->faker->email()
        ];
    }
}
