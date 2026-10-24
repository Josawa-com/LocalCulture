<?php

namespace Database\Factories;

use App\Models\CultureCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CultureCategory> */
class CultureCategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Rumah Adat', 'Pakaian Adat', 'Tarian Tradisional', 'Alat Musik Tradisional', 'Kuliner Khas', 'Tradisi & Kesenian',
        ]);

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
            'icon' => '🎭',
        ];
    }
}
