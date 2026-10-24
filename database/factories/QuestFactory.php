<?php

namespace Database\Factories;

use App\Models\Quest;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Quest> */
class QuestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['daily', 'weekly']),
            'title' => 'Quest '.fake()->words(2, true),
            'description' => fake()->sentence(),
            'available_on' => today(),
            'ends_at' => null,
            'time_limit' => 7,
            'max_attempts' => 1,
            'is_active' => true,
        ];
    }
}
