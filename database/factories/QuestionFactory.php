<?php

namespace Database\Factories;

use App\Models\Quest;
use App\Models\Question;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Question> */
class QuestionFactory extends Factory
{
    public function definition(): array
    {
        $options = fake()->sentences(4);

        return [
            'quest_id' => Quest::factory(),
            'question' => fake()->sentence().'?',
            'options' => $options,
            'correct_index' => fake()->numberBetween(0, 3),
            'points' => 100,
            'sort_order' => 0,
        ];
    }
}
