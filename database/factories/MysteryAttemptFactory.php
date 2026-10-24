<?php

namespace Database\Factories;

use App\Models\Mystery;
use App\Models\MysteryAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<MysteryAttempt> */
class MysteryAttemptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'mystery_id' => Mystery::factory(),
            'guess' => fake()->words(2, true),
            'is_correct' => fake()->boolean(40),
            'clues_used' => fake()->numberBetween(1, 3),
            'score' => fake()->numberBetween(0, 300),
        ];
    }
}
