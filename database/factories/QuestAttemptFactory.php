<?php

namespace Database\Factories;

use App\Models\Quest;
use App\Models\QuestAttempt;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<QuestAttempt> */
class QuestAttemptFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quest_id' => Quest::factory(),
            'user_id' => User::factory(),
            'started_at' => now(),
            'finished_at' => now(),
            'total_score' => fake()->numberBetween(0, 500),
            'correct_count' => fake()->numberBetween(0, 5),
            'total_time_ms' => fake()->numberBetween(5000, 35000),
            'status' => 1,
        ];
    }
}
