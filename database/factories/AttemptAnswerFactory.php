<?php

namespace Database\Factories;

use App\Models\AttemptAnswer;
use App\Models\QuestAttempt;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AttemptAnswer> */
class AttemptAnswerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'quest_attempt_id' => QuestAttempt::factory(),
            'question_id' => Question::factory(),
            'selected_index' => fake()->numberBetween(0, 3),
            'is_correct' => fake()->boolean(50),
            'time_ms' => fake()->numberBetween(1000, 7000),
            'score' => fake()->numberBetween(0, 100),
            'shown_at' => now(),
            'answered_at' => now(),
        ];
    }
}
