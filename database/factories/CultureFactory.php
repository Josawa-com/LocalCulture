<?php

namespace Database\Factories;

use App\Models\Culture;
use App\Models\CultureCategory;
use App\Models\Regency;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Culture> */
class CultureFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'regency_id' => Regency::factory(),
            'culture_category_id' => CultureCategory::factory(),
            'name' => str($name)->title(),
            'slug' => str($name)->slug(),
            'description' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'philosophy' => fake()->randomElement([null, fake()->paragraph()]),
            'sort_order' => 0,
        ];
    }
}
