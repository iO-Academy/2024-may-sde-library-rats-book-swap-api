<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            DB::table('books')->insert([
                'title' => $faker->word(),
                'author' => $faker->name(),
                'year' => $faker->year(),
                'blurb' => $faker->sentence(),
                'image' => $faker->imageUrl(),
                'claimed_by_name' => null,
                'page_count' => rand(100, 1000),
                'claimed' => 0,
                'genre_id' => rand(1, 4),
                'user_id' => null
            ]);
        }
    }
}
