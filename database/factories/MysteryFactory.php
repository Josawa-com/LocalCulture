<?php

namespace Database\Factories;

use App\Models\Mystery;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Mystery> */
class MysteryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'answer' => fake()->unique()->words(2, true),
            'origin' => fake()->city(),
            'clues' => [fake()->sentence(), fake()->sentence(), fake()->sentence()],
            'max_clues' => 3,
            'time_limit' => 60,
            'is_active' => true,
        ];
    }
}
