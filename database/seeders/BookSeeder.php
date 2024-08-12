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

            DB::table('books')->insert([
                'title' => $faker->words(2),
                'author' => $faker->name(),
                'year' => $faker->year(),
                'blurb' => $faker->sentence(50),
                'image' => $faker->imageUrl(),
                'claimed_by_name' => null,
                'page_count' => rand(100, 1000),
                'claimed' => 0,
                'genre_id' => rand(1, 4),
                'user_id' => null
            ]);
    }
}
