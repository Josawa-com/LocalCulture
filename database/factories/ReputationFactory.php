<?php

namespace Database\Factories;

use App\Models\Reputation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Reputation> */
class ReputationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'total_score' => fake()->numberBetween(0, 3000),
            'quests_completed' => fake()->numberBetween(0, 20),
            'mysteries_solved' => fake()->numberBetween(0, 10),
            'streak' => fake()->numberBetween(0, 7),
        ];
    }
}
